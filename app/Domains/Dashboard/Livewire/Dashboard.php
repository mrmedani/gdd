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

    // Category detail popup
    public ?int $selectedCategoryId = null;
    public bool $categoryModalOpen = false;
    public array $categoryModalData = [];

    public string $alertFilterType = '';
    public string $alertFilterSeverity = '';

    public string $greeting = '';
    public string $greetingIcon = '';
    public string $greetingGradient = '';
    public string $roleLabel = '';

    protected $queryString = ['alertFilterType', 'alertFilterSeverity'];

    public function mount(): void
    {
        $now = Carbon::now();

        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 12) {
            $this->greeting = __('dashboard.greeting_morning');
            $this->greetingIcon = 'sun';
            $this->greetingGradient = 'from-amber-400 to-orange-500';
        } elseif ($hour >= 12 && $hour < 17) {
            $this->greeting = __('dashboard.greeting_afternoon');
            $this->greetingIcon = 'cloud-sun';
            $this->greetingGradient = 'from-sky-400 to-blue-500';
        } elseif ($hour >= 17 && $hour < 20) {
            $this->greeting = __('dashboard.greeting_evening');
            $this->greetingIcon = 'sunset';
            $this->greetingGradient = 'from-purple-500 to-pink-500';
        } else { // 20h00 -> 04h59 = nuit
            $this->greeting = __('dashboard.greeting_night');
            $this->greetingIcon = 'moon';
            $this->greetingGradient = 'from-indigo-500 to-violet-600';
        }

        if ($role = auth()->user()->role) {
            $this->roleLabel = match (app()->getLocale()) {
                'ar' => $role->label_ar ?: $role->label_fr ?: $role->name,
                'fr' => $role->label_fr ?: $role->label_ar ?: $role->name,
                default => $role->name ?: $role->label_fr,
            };
        }
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
                'id' => $cat->id,
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

    public function openCategory(int $categoryId): void
    {
        $now = Carbon::now();
        $currentPeriod = getPeriodFromDate($now);
        $range = getPeriodRange($currentPeriod);

        $cat = \App\Domains\Expenses\Models\ExpenseCategory::find($categoryId);
        if (!$cat) return;

        $expenses = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->where('category_id', $categoryId)
            ->orderByDesc('amount')
            ->get();

        $total = (float) $expenses->sum('amount');
        $count = $expenses->count();

        $repetitive = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->where('category_id', $categoryId)
            ->whereNotNull('description')
            ->selectRaw('description, COUNT(*) as cnt, SUM(amount) as sum')
            ->groupBy('description')
            ->orderByDesc('cnt')
            ->get()
            ->map(fn($r) => ['description' => $r->description, 'count' => (int) $r->cnt, 'total' => (float) $r->sum])
            ->filter(fn($r) => $r['count'] > 1)
            ->values()
            ->toArray();

        $this->categoryModalData = [
            'id' => $cat->id,
            'label' => $cat->translated_name,
            'color' => $this->categoryColor($cat->id),
            'total' => $total,
            'count' => $count,
            'pct' => 0,
            'avg' => $count > 0 ? round($total / $count, 2) : 0,
            'max' => $count > 0 ? (float) $expenses->max('amount') : 0,
            'min' => $count > 0 ? (float) $expenses->min('amount') : 0,
            'top3' => $expenses->take(10)->map(fn($e) => [
                'date' => $e->date->format('d/m/Y'),
                'description' => $e->description,
                'amount' => (float) $e->amount,
            ])->toArray(),
            'repetitive' => $repetitive,
        ];
        $this->selectedCategoryId = $categoryId;
        $this->categoryModalOpen = true;
    }

    public function closeCategory(): void
    {
        $this->categoryModalOpen = false;
        $this->selectedCategoryId = null;
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
