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

    // Deficit
    public float $dailyTotal = 0;
    public float $cashDeficit = 0;
    public array $deficitHistory = [];

    // History
    public array $closureHistory = [];

    public function mount(): void
    {
        Gate::authorize('manage-statistics');
        $this->period = getPeriodFromDate(now());
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
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
        $expenses = Expense::whereBetween('date', [$range['start'], $range['end']])->get();
        $daysInPeriod = max(1, $range['start']->diffInDays($range['end']));

        $this->totalExpenses = $expenses->sum('amount');
        $this->expenseCount = $expenses->count();
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
        $categories = ExpenseCategory::active()->get();
        $this->expensesByCategory = $categories->map(function ($cat) use ($expenses) {
            $total = $expenses->where('category_id', $cat->id)->sum('amount');
            return [
                'label' => $cat->translated_name,
                'total' => (float) $total,
                'pct' => $this->totalExpenses > 0 ? round($total / $this->totalExpenses * 100, 1) : 0,
                'count' => $expenses->where('category_id', $cat->id)->count(),
                'color' => $this->categoryColor($cat->id),
            ];
        })->filter(fn($c) => $c['total'] > 0)->sortByDesc('total')->values()->toArray();

        // Payment method breakdown
        $methods = PaymentMethod::cases();
        $this->paymentMethodData = collect($methods)->map(function ($method) use ($expenses) {
            $total = $expenses->where('payment_method', $method->value)->sum('amount');
            return [
                'label' => __('payment_methods.' . $method->value),
                'total' => (float) $total,
                'pct' => $this->totalExpenses > 0 ? round($total / $this->totalExpenses * 100, 1) : 0,
                'count' => $expenses->where('payment_method', $method->value)->count(),
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
        $allTrendExpenses = Expense::whereBetween('date', [$trendStart, $trendEnd])
            ->selectRaw('date, amount')
            ->get();
        $this->monthlyTrend = $trendPeriods->map(function ($tp) use ($allTrendExpenses) {
            $total = $allTrendExpenses->filter(fn($e) => $e->date->between($tp['range']['start'], $tp['range']['end']))->sum('amount');
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
    }

    private function categoryColor(int $id): string
    {
        $hue = fmod(($id - 1) * 137.508, 360);
        $sat = 55 + fmod(($id - 1) * 73, 30);
        $lit = 40 + fmod(($id - 1) * 47, 30);
        return "hsl({$hue}, {$sat}%, {$lit}%)";
    }
}
