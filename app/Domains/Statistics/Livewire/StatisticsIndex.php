<?php

namespace App\Domains\Statistics\Livewire;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Shared\Enums\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class StatisticsIndex extends Component
{
    use WithPagination;

    public string $period = '';

    // KPIs
    public float $totalExpenses = 0;
    public float $totalGains = 0;
    public float $balance = 0;
    public int $expenseCount = 0;
    public float $averagePerDay = 0;
    public float $previousExpenses = 0;
    public float $expensesChange = 0;

    // Charts
    public array $expensesByCategory = [];
    public array $paymentMethodData = [];
    public array $monthlyTrend = [];
    public array $growthTrend = [];

    // Category detail popup
    public array $repetitiveByCategory = [];
    public ?int $selectedCategoryId = null;
    public bool $categoryModalOpen = false;
    public array $categoryModalData = [];

    // Deficit
    public float $dailyTotal = 0;
    public float $cashDeficit = 0;
    public array $deficitHistory = [];

    // History
    public array $closureHistory = [];

    // Entrées d'argent (module Treasury/Incomes)
    public float $incomeTotal = 0;
    public array $incomeByTypeLabels = [];
    public array $incomeByTypeValues = [];
    public array $incomeByTypeColors = [];
    public array $incomeTrend = [];

    public function mount(): void
    {
        Gate::authorize('manage-statistics');
        $this->period = getPeriodFromDate(now());
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
        $this->categoryModalOpen = false;
        $this->selectedCategoryId = null;
        $this->categoryModalData = [];
    }

    public function openCategory(int $categoryId): void
    {
        $range = getPeriodRange($this->period);
        $cat = ExpenseCategory::find($categoryId);
        if (!$cat) return;

        $expenses = Expense::whereBetween('date', [$range['start'], $range['end']])
            ->where('category_id', $categoryId)
            ->orderByDesc('amount')
            ->get();

        $total = (float) $expenses->sum('amount');
        $count = $expenses->count();
        $top3 = $expenses->take(10)->map(fn($e) => [
            'date' => $e->date->format('d/m/Y'),
            'description' => $e->description,
            'amount' => (float) $e->amount,
        ])->toArray();

        $repetitive = collect($this->repetitiveByCategory[$categoryId] ?? [])
            ->filter(fn($r) => $r['count'] > 1)
            ->values()
            ->toArray();

        $this->categoryModalData = [
            'id' => $cat->id,
            'label' => $cat->translated_name,
            'color' => $this->categoryColor($cat->id),
            'total' => $total,
            'count' => $count,
            'pct' => $this->totalExpenses > 0 ? round($total / $this->totalExpenses * 100, 1) : 0,
            'avg' => $count > 0 ? round($total / $count, 2) : 0,
            'max' => $count > 0 ? (float) $expenses->max('amount') : 0,
            'min' => $count > 0 ? (float) $expenses->min('amount') : 0,
            'top3' => $top3,
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

    public function render()
    {
        $this->loadStats();

        $earliestExpense = Expense::orderBy('date')->value('date');
        $earliestClosure = MonthlyClosure::orderBy('month')->value('month');
        $startPeriod = $earliestClosure
            ? $earliestClosure
            : ($earliestExpense ? getPeriodFromDate($earliestExpense) : now()->format('Y-m'));
        $nowPeriod = getPeriodFromDate(now());
        $periods = [];
        $cursor = \Carbon\Carbon::createFromFormat('!Y-m', $nowPeriod)->startOfMonth();
        $end = \Carbon\Carbon::createFromFormat('!Y-m', $startPeriod)->startOfMonth();
        while ($cursor->greaterThanOrEqualTo($end)) {
            $periods[] = $cursor->format('Y-m');
            $cursor->subMonth();
        }

        $expenses = $this->getExpensesQuery()->paginate(15);

        return view('livewire.statistics-index', [
            'periods' => $periods,
            'expenses' => $expenses,
        ])->layout('layouts.app')->title(__('nav.statistics'));
    }

    private function getExpensesQuery()
    {
        $range = getPeriodRange($this->period);
        return Expense::with('category', 'employee')
            ->whereBetween('date', [$range['start'], $range['end']])
            ->latest('date');
    }

    private function loadStats(): void
    {
        if (!$this->period) return;

        $range = getPeriodRange($this->period);
        $daysInPeriod = max(1, $range['start']->diffInDays($range['end']));

        $this->totalExpenses = (float) Expense::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->expenseCount = Expense::whereBetween('date', [$range['start'], $range['end']])->count();
        $this->averagePerDay = round($this->totalExpenses / $daysInPeriod, 2);

        // Previous period comparison
        $prevPeriodMonth = Carbon::createFromFormat('Y-m', $this->period)->subMonth()->format('Y-m');
        $prevRange = getPeriodRange($prevPeriodMonth);
        $this->previousExpenses = Expense::whereBetween('date', [$prevRange['start'], $prevRange['end']])->sum('amount');
        $this->expensesChange = $this->previousExpenses > 0
            ? round(($this->totalExpenses - $this->previousExpenses) / $this->previousExpenses * 100, 1)
            : 0;

        // Closure data
        $closure = MonthlyClosure::where('month', $this->period)->first();
        if ($closure) {
            $this->totalGains = (float) $closure->gains;
            $this->balance = (float) $closure->balance;
        }

        // Category breakdown
        $categories = ExpenseCategory::active()->get()->keyBy('id');
        $byCategory = Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');
        $this->expensesByCategory = $categories->map(function ($cat) use ($byCategory) {
            $row = $byCategory->get($cat->id);
            $total = (float) ($row->total ?? 0);
            return [
                'id' => $cat->id,
                'label' => $cat->translated_name,
                'total' => $total,
                'pct' => $this->totalExpenses > 0 ? round($total / $this->totalExpenses * 100, 1) : 0,
                'count' => (int) ($row->count ?? 0),
                'color' => $this->categoryColor($cat->id),
            ];
        })->filter(fn($c) => $c['total'] > 0)->sortByDesc('total')->values()->toArray();

        // Pre-load repetitive descriptions per category for the popup
        $repById = [];
        foreach ($categories as $cat) {
            $repById[$cat->id] = Expense::whereBetween('date', [$range['start'], $range['end']])
                ->where('category_id', $cat->id)
                ->whereNotNull('description')
                ->selectRaw('description, COUNT(*) as cnt, SUM(amount) as sum')
                ->groupBy('description')
                ->orderByDesc('cnt')
                ->limit(5)
                ->get()
                ->map(fn($r) => ['description' => $r->description, 'count' => (int) $r->cnt, 'total' => (float) $r->sum])
                ->toArray();
        }
        $this->repetitiveByCategory = $repById;

        // Payment method breakdown
        $methods = PaymentMethod::cases();
        $byMethod = Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');
        $this->paymentMethodData = collect($methods)->map(function ($method) use ($byMethod) {
            $row = $byMethod->get($method->value);
            $total = (float) ($row->total ?? 0);
            return [
                'label' => __('payment_methods.' . $method->value),
                'total' => $total,
                'pct' => $this->totalExpenses > 0 ? round($total / $this->totalExpenses * 100, 1) : 0,
                'count' => (int) ($row->count ?? 0),
            ];
        })->filter(fn($c) => $c['total'] > 0)->values()->toArray();

        // 12-month trend (single batch query)
        $trendPeriods = collect(range(0, 11))->map(fn($i) => [
            'period' => $p = getPeriodFromDate(now()->subMonths($i)),
            'range' => getPeriodRange($p),
            'date' => Carbon::createFromFormat('Y-m', $p),
        ]);
        $trendStart = $trendPeriods->min('range.start');
        $trendEnd = $trendPeriods->max('range.end');
        $startDay = getMonthPeriodStartDay();
        $periodTrendTotals = Expense::whereBetween('date', [$trendStart, $trendEnd])
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
        $this->monthlyTrend = $trendPeriods->map(function ($tp) use ($periodTrendTotals) {
            $total = (float) ($periodTrendTotals[$tp['period']] ?? 0);
            return [
                'month' => $tp['date']->translatedFormat('M Y'),
                'total' => (float) $total,
            ];
        })->reverse()->values()->toArray();

        $this->dailyTotal = (float) Expense::whereDate('date', today())->sum('amount');

        // Cash deficit
        $this->cashDeficit = (float) Setting::get('cash_deficit', 0);

        // Deficit deduction/increase history
        $this->deficitHistory = Alert::whereIn('type', ['deficit_deducted', 'deficit_increased'])
            ->latest()
            ->take(50)
            ->get()
            ->map(fn(Alert $a) => [
                'month' => formatPeriodLabel($a->data['closure_month'] ?? ''),
                'type' => $a->type,
                'amount' => (float) ($a->data['deduction'] ?? $a->data['increase'] ?? 0),
                'remaining' => (float) ($a->data['remaining'] ?? $a->data['new_total'] ?? 0),
                'date' => $a->created_at->format('d/m/Y H:i'),
            ])->toArray();

        // Closure history
        $this->closureHistory = MonthlyClosure::with('closer')
            ->latest()
            ->take(12)
            ->get()
            ->map(fn($c) => [
                'label' => formatPeriodLabel($c->month),
                'gains' => (float) $c->gains,
                'expenses' => (float) $c->expenses,
                'balance' => (float) $c->balance,
                'closed_by' => $c->closer?->name ?? '-',
                'date' => $c->created_at->format('d/m/Y H:i'),
            ])->toArray();

        // Growth trend (Évolution des gains brute)
        $closures = MonthlyClosure::orderBy('month', 'asc')->get(['month', 'gains']);
        $this->growthTrend = $closures->map(function ($c) {
            return [
                'label' => formatPeriodLabel($c->month),
                'rate' => (float) $c->gains,
            ];
        })->toArray();

        // Entrées d'argent (module Treasury/Incomes)
        $incomeTotals = \App\Domains\Treasury\Models\Income::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('source_type, SUM(amount) as total')
            ->groupBy('source_type')
            ->get()
            ->keyBy('source_type');

        $incomeTypeLabels = [
            'investment' => 'Investissement',
            'franchise_fee' => 'Droits de franchise',
            'sale' => 'Vente',
            'other' => 'Autre',
        ];
        $incomeTypeOrder = ['investment', 'franchise_fee', 'other'];
        $incomeColors = ['#10b981', '#3b82f6', '#64748b'];
        $this->incomeTotal = (float) \App\Domains\Treasury\Models\Income::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->incomeByTypeLabels = [];
        $this->incomeByTypeValues = [];
        $this->incomeByTypeColors = [];
        foreach ($incomeTypeOrder as $i => $key) {
            $total = (float) ($incomeTotals->get($key)->total ?? 0);
            if ($total <= 0) continue;
            $this->incomeByTypeLabels[] = $incomeTypeLabels[$key];
            $this->incomeByTypeValues[] = $total;
            $this->incomeByTypeColors[] = $incomeColors[$i];
        }

        // Tendance 12 mois des entrées
        $trendPeriods = collect(range(0, 11))->map(function ($i) {
            $p = getPeriodFromDate(now()->subMonths($i));
            $r = getPeriodRange($p);
            return ['key' => $p, 'start' => $r['start'], 'end' => $r['end'], 'date' => \Carbon\Carbon::createFromFormat('Y-m', $p)];
        });
        $trendStart = $trendPeriods->min('start');
        $trendEnd = $trendPeriods->max('end');
        $incomeTrendTotals = \App\Domains\Treasury\Models\Income::whereBetween('date', [$trendStart, $trendEnd])
            ->selectRaw('DATE_FORMAT(date, ?) as period, SUM(amount) as total', [$startDay])
            ->groupBy('period')
            ->pluck('total', 'period');
        $this->incomeTrend = $trendPeriods->map(function ($tp) use ($incomeTrendTotals) {
            $periodKey = $tp['date']->format('Y-m');
            return [
                'month' => $tp['date']->translatedFormat('M Y'),
                'total' => (float) ($incomeTrendTotals[$periodKey] ?? 0),
            ];
        })->reverse()->values()->toArray();
    }

    private function categoryColor(int $id): string
    {
        $hue = fmod(($id - 1) * 137.508, 360);
        $sat = 55 + fmod(($id - 1) * 73, 30);
        $lit = 40 + fmod(($id - 1) * 47, 30);
        return "hsl({$hue}, {$sat}%, {$lit}%)";
    }
}
