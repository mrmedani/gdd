<?php

namespace App\Domains\Dashboard\Livewire;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
class Dashboard extends Component
{
    use WithPagination;

    public float $monthlyTotal = 0;
    public float $monthlyCount = 0;
    public float $averagePerDay = 0;
    public array $categoryData = [];
    public array $monthlyTrend = [];
    public array $recentExpenses = [];
    public int $unreadAlerts = 0;
    public bool $showAlertsModal = false;
    public int $totalUsers = 0;
    public int $totalAuditLogs = 0;
    public string $currentPeriodLabel = '';
    public int $remainingDays = 0;
    public float $cashDeficit = 0;
    public float $dailyTotal = 0;
    public string $periodStartDate = '';
    public string $periodEndDate = '';
    public ?float $growthRate = null;

    public string $alertFilterType = '';
    public string $alertFilterSeverity = '';

    public string $greeting = '';
    public string $greetingIcon = '';
    public string $greetingColor = '';
    public string $greetingBg = '';
    public string $serverDate = '';
    public string $serverTime = '';
    public int $serverTimestamp = 0;

    protected $queryString = ['alertFilterType', 'alertFilterSeverity'];

    public function mount(): void
    {
        $now = Carbon::now();

        $hour = (int) $now->format('H');
        if ($hour < 12) {
            $this->greeting = __('dashboard.greeting_morning');
            $this->greetingIcon = 'sun';
            $this->greetingColor = 'text-amber-400';
            $this->greetingBg = 'from-amber-400/20 to-orange-500/10';
        } elseif ($hour < 17) {
            $this->greeting = __('dashboard.greeting_afternoon');
            $this->greetingIcon = 'cloud-sun';
            $this->greetingColor = 'text-sky-400';
            $this->greetingBg = 'from-sky-400/20 to-blue-500/10';
        } elseif ($hour < 20) {
            $this->greeting = __('dashboard.greeting_evening');
            $this->greetingIcon = 'sunset';
            $this->greetingColor = 'text-purple-400';
            $this->greetingBg = 'from-purple-500/20 to-pink-500/10';
        } else {
            $this->greeting = __('dashboard.greeting_night');
            $this->greetingIcon = 'moon';
            $this->greetingColor = 'text-indigo-400';
            $this->greetingBg = 'from-indigo-500/20 to-violet-600/10';
        }

        $appLocale = app()->getLocale();
        $this->serverDate = $now->locale($appLocale)->translatedFormat('l j F Y');
        $this->serverTime = $now->format('H:i:s');
        $this->serverTimestamp = $now->timestamp;
        $currentPeriod = getPeriodFromDate($now);
        $range = getPeriodRange($currentPeriod);
        $this->remainingDays = max(0, $now->diffInDays($range['end'], false));
        $this->periodStartDate = $range['start']->format('Y-m-d');
        $this->periodEndDate = $range['end']->format('Y-m-d');
        $periodDays = $range['start']->diffInDays($range['end']);

        $this->monthlyTotal = (float) Expense::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->monthlyCount = Expense::whereBetween('date', [$range['start'], $range['end']])->count();
        $this->averagePerDay = $periodDays > 0 ? round($this->monthlyTotal / $periodDays, 2) : 0;

        $this->dailyTotal = (float) Expense::whereDate('date', today())->sum('amount');

        $categoryTotals = Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $categories = ExpenseCategory::active()->get();
        $this->categoryData = $categories->map(function ($cat) use ($categoryTotals) {
            $total = (float) ($categoryTotals[$cat->id] ?? 0);
            return [
                'label' => $cat->translated_name,
                'total' => $total,
                'color' => $this->categoryColor($cat->id),
            ];
        })->filter(fn($c) => $c['total'] > 0)->values()->toArray();

        $monthlyClosures = MonthlyClosure::whereBetween('month', [
            $now->copy()->subMonths(11)->format('Y-m'),
            $now->format('Y-m'),
        ])->get()->keyBy('month');

        $periods12 = collect(range(0, 11))->map(function ($i) use ($now) {
            $p = getPeriodFromDate($now->copy()->subMonths($i));
            $r = getPeriodRange($p);
            return ['key' => $p, 'start' => $r['start'], 'end' => $r['end']];
        });

        $startDay = getMonthPeriodStartDay();

        $periodTotals = Expense::whereBetween('date', [$periods12->last()['start'], $periods12->first()['end']])
            ->selectRaw("
                DATE_FORMAT(
                    CASE 
                        WHEN DAY(date) > ? THEN DATE_ADD(date, INTERVAL 1 MONTH)
                        ELSE date
                    END, 
                    '%Y-%m'
                ) as period, 
                SUM(amount) as total
            ", [$startDay])
            ->groupBy('period')
            ->pluck('total', 'period');

        $expenseTotals = $periodTotals->map(fn($v) => (float) $v)->toArray();

        $this->monthlyTrend = $periods12->map(function ($p, $i) use ($now, $monthlyClosures, $expenseTotals) {
            $expensesTotal = (float) ($expenseTotals[$p['key']] ?? 0);
            $calMonth = $now->copy()->subMonths($i)->format('Y-m');
            $closure = $monthlyClosures->get($calMonth);
            $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $p['key']);
            return [
                'month' => $periodDate->translatedFormat('M Y'),
                'expenses' => $expensesTotal,
                'gains' => $closure ? (float) $closure->gains : 0,
                'balance' => $closure ? (float) $closure->balance : 0,
            ];
        })->reverse()->values()->toArray();

        $this->currentPeriodLabel = formatPeriodLabel($currentPeriod);

        $this->cashDeficit = (float) Setting::get('cash_deficit', 0);

        // Taux de croissance mois par mois (dernière clôture vs précédente)
        $lastTwo = MonthlyClosure::orderBy('month', 'desc')->take(2)->pluck('gains');
        if ($lastTwo->count() === 2) {
            $current = (float) $lastTwo->first();
            $previous = (float) $lastTwo->last();
            $this->growthRate = $previous > 0
                ? round(($current - $previous) / $previous * 100, 1)
                : null;
        }

        $this->recentExpenses = Expense::with('category')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'date' => $e->date->format('Y-m-d'),
                'description' => $e->description,
                'amount' => $e->amount,
                'category' => $e->category?->translated_name ?? __("categories.{$e->category_key}"),
            ])->toArray();

        $this->loadUnreadCount();

        if (auth()->user()?->isAdmin()) {
            $this->totalUsers = User::count();
            $this->totalAuditLogs = AuditLog::count();
        }
    }

    public function render()
    {
        $alerts = $this->getFilteredAlerts();

        return view('livewire.dashboard', [
            'alertsPaginated' => $alerts,
        ])
            ->layout('layouts.app')
            ->title(__('nav.dashboard'));
    }

    public function refreshServerTime(): void
    {
        $now = Carbon::now();
        $appLocale = app()->getLocale();
        $this->serverDate = $now->locale($appLocale)->translatedFormat('l j F Y');
        $this->serverTime = $now->format('H:i:s');
        $this->serverTimestamp = $now->timestamp;
    }

    public function loadUnreadCount(): void
    {
        $query = Alert::unread();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        $this->unreadAlerts = $query->count();
    }

    private function getFilteredAlerts()
    {
        $query = Alert::latest();

        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }

        if ($this->alertFilterType) {
            $query->where('type', $this->alertFilterType);
        }

        if ($this->alertFilterSeverity) {
            $query->where('severity', $this->alertFilterSeverity);
        }

        return $query->paginate(15);
    }

    public function getAlertTypesProperty(): array
    {
        return Alert::select('type')->distinct()->pluck('type')->toArray();
    }

    public function getAlertSeveritiesProperty(): array
    {
        return ['info', 'warning', 'error', 'success'];
    }

    public function filterByType(string $type): void
    {
        $this->alertFilterType = $this->alertFilterType === $type ? '' : $type;
        $this->resetPage();
    }

    public function filterBySeverity(string $severity): void
    {
        $this->alertFilterSeverity = $this->alertFilterSeverity === $severity ? '' : $severity;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->alertFilterType = '';
        $this->alertFilterSeverity = '';
        $this->resetPage();
    }

    public function markAlertRead(int $id): void
    {
        $alert = Alert::find($id);
        if ($alert) {
            $alert->update(['is_read' => true, 'read_at' => now()]);
            $this->loadUnreadCount();
        }
    }

    public function markAllAlertsRead(): void
    {
        Alert::unread()->update(['is_read' => true, 'read_at' => now()]);
        $this->loadUnreadCount();
    }

    private function categoryColor(int $id): string
    {
        $palette = [
            '#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#6366F1',
            '#84CC16', '#D946EF', '#0EA5E9', '#EAB308', '#22C55E',
        ];
        return $palette[($id - 1) % count($palette)];
    }
}
