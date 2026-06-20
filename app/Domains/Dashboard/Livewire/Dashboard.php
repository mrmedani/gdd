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

    public string $alertFilterType = '';
    public string $alertFilterSeverity = '';

    protected $queryString = ['alertFilterType', 'alertFilterSeverity'];

    public function mount(): void
    {
        $now = Carbon::now();
        $currentPeriod = getPeriodFromDate($now);
        $range = getPeriodRange($currentPeriod);
        $this->remainingDays = max(0, $now->diffInDays($range['end'], false));
        $this->periodStartDate = $range['start']->format('Y-m-d');
        $this->periodEndDate = $range['end']->format('Y-m-d');
        $periodDays = $range['start']->diffInDays($range['end']);

        $monthExpenses = Expense::whereBetween('date', [$range['start'], $range['end']])->get();

        $this->monthlyTotal = $monthExpenses->sum('amount');
        $this->monthlyCount = $monthExpenses->count();
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

        $allExpenses = Expense::whereBetween('date', [$periods12->last()['start'], $periods12->first()['end']])
            ->get(['date', 'amount']);

        $expenseTotals = [];
        foreach ($allExpenses as $ex) {
            $pk = getPeriodFromDate($ex->date);
            $expenseTotals[$pk] = ($expenseTotals[$pk] ?? 0) + $ex->amount;
        }

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
