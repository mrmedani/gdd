<?php

namespace App\Domains\Statistics\Livewire;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Shared\Enums\PaymentMethod;
use App\Domains\Statistics\Exports\StatisticsPeriodExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use App\Shared\Livewire\WithToast;

class StatisticsIndex extends Component
{
    use WithPagination, WithToast;

    public string $period = '';

    // KPIs
    public float $totalExpenses = 0;
    public float $totalGains = 0;
    public float $balance = 0;
    public int $expenseCount = 0;
    public float $averagePerDay = 0;
    public float $previousExpenses = 0;
    public ?float $expensesChange = null;

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

    // Commissions stations
    public float $stationChargesTotal = 0;
    public float $stationPaidTotal = 0;
    public float $stationUnpaidTotal = 0;
    public array $stationChargesRows = [];

    // Entrées d'argent (module Treasury/Incomes)
    public float $incomeTotal = 0;
    public array $incomeByTypeLabels = [];
    public array $incomeByTypeValues = [];
    public array $incomeByTypeColors = [];
    public array $incomeTrend = [];

    public function mount(): void
    {
        Gate::authorize('manage-statistics');
        // ?period=YYYY-MM possible (navigation depuis le dashboard : clic sur un mois
        // de la tendance). Fallback : période courante. Période invalide ignorée.
        $requested = request('period');
        $valid = $requested && preg_match('/^\d{4}-\d{2}$/', $requested) && getPeriodRange($requested) !== null;
        $this->period = $valid ? $requested : getPeriodFromDate(now());
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
        // CACHE par période + version (StatsCacheInvalidator::bump() à toute mutation).
        // La clé intègre le version pour purger TOUT le préfixe en un incrément.
        $stats = \Illuminate\Support\Facades\Cache::remember(
            \App\Domains\Statistics\Support\StatsCacheInvalidator::key($this->period),
            now()->addHour(),
            fn () => $this->computeStats($this->period)
        );

        $this->totalExpenses = (float) ($stats['totalExpenses'] ?? 0);
        $this->expenseCount = (int) ($stats['expenseCount'] ?? 0);
        $this->averagePerDay = round($this->totalExpenses / max(1, (int) ($stats['daysInPeriod'] ?? 1)), 2);
        $this->previousExpenses = (float) ($stats['previousExpenses'] ?? 0);
        $this->expensesChange = $this->previousExpenses > 0
            ? round(($this->totalExpenses - $this->previousExpenses) / $this->previousExpenses * 100, 1)
            : null;
        $this->totalGains = (float) ($stats['totalGains'] ?? 0);
        $this->balance = (float) ($stats['balance'] ?? 0);
        $this->expensesByCategory = (array) ($stats['expensesByCategory'] ?? []);
        $this->repetitiveByCategory = (array) ($stats['repetitiveByCategory'] ?? []);
        $this->paymentMethodData = (array) ($stats['paymentMethodData'] ?? []);
        $this->monthlyTrend = (array) ($stats['monthlyTrend'] ?? []);
        $this->dailyTotal = (float) ($stats['dailyTotal'] ?? 0);
        $this->cashDeficit = (float) ($stats['cashDeficit'] ?? 0);
        $this->deficitHistory = (array) ($stats['deficitHistory'] ?? []);
        $this->closureHistory = (array) ($stats['closureHistory'] ?? []);
        $this->growthTrend = (array) ($stats['growthTrend'] ?? []);
        $this->incomeTotal = (float) ($stats['incomeTotal'] ?? 0);
        $this->incomeByTypeLabels = (array) ($stats['incomeByTypeLabels'] ?? []);
        $this->incomeByTypeValues = (array) ($stats['incomeByTypeValues'] ?? []);
        $this->incomeByTypeColors = (array) ($stats['incomeByTypeColors'] ?? []);
        $this->incomeTrend = (array) ($stats['incomeTrend'] ?? []);

        // Commissions stations (nouveaux module) — visibles aussi en stats
        $this->stationChargesTotal = (float) ($stats['stationChargesTotal'] ?? 0);
        $this->stationPaidTotal = (float) ($stats['stationPaidTotal'] ?? 0);
        $this->stationUnpaidTotal = max(0, $this->stationChargesTotal - $this->stationPaidTotal);
        $this->stationChargesRows = (array) ($stats['stationChargesRows'] ?? []);
    }

    /** Toutes les statistiques de la période — CALCUL PUR, résultat listo au cache. */
    private function computeStats(string $period): array
    {
        if (!$period) return [];

        $now = now();
        $range = getPeriodRange($period);
        $daysInPeriod = $range['start']->diffInDays($range['end']);
        $totalExpenses = (float) \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $expenseCount = (int) \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])->count();

        // Comparaison période précédente (subMonthNoOverflow — pas subMonth !)
        $prevPeriodMonth = \Carbon\Carbon::createFromFormat('Y-m', $period)
            ->subMonthNoOverflow()
            ->format('Y-m');
        $prevRange = getPeriodRange($prevPeriodMonth);
        $previousExpenses = (float) \App\Domains\Expenses\Models\Expense::whereBetween('date', [$prevRange['start'], $prevRange['end']])->sum('amount');

        $closure = \App\Domains\Treasury\Models\MonthlyClosure::where('month', $period)->first();

        // Découpage par catégorie
        $categories = \App\Domains\Expenses\Models\ExpenseCategory::active()->get()->keyBy('id');
        $byCategory = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');
        $expensesByCategory = $categories->map(function ($cat) use ($byCategory, $totalExpenses) {
            $row = $byCategory->get($cat->id);
            $total = (float) ($row->total ?? 0);
            return [
                'id' => $cat->id,
                'label' => $cat->translated_name,
                'total' => $total,
                'pct' => $totalExpenses > 0 ? round($total / $totalExpenses * 100, 1) : 0,
                'count' => (int) ($row->count ?? 0),
                'color' => $this->categoryColor($cat->id),
            ];
        })->filter(fn ($c) => $c['total'] > 0)->sortByDesc('total')->values()->toArray();

        // Répétitives par catégorie : UNE SEULE requête groupée (fini le N+1)
        $repRows = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->whereNotNull('description')
            ->selectRaw('category_id, description, COUNT(*) as cnt, SUM(amount) as sum')
            ->groupBy('category_id', 'description')
            ->get();
        $repetitiveByCategory = [];
        foreach ($repRows->groupBy('category_id') as $catId => $rows) {
            $repetitiveByCategory[(int) $catId] = $rows
                ->map(fn ($r) => ['description' => $r->description, 'count' => (int) $r->cnt, 'total' => (float) $r->sum])
                ->sortByDesc('count')
                ->take(5)
                ->values()
                ->toArray();
        }

        // Découpage par moyen de paiement
        $methods = \App\Shared\Enums\PaymentMethod::cases();
        $byMethod = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');
        $paymentMethodData = collect($methods)->map(function ($method) use ($byMethod, $totalExpenses) {
            $row = $byMethod->get($method->value);
            $total = (float) ($row->total ?? 0);
            return [
                'label' => __('payment_methods.' . $method->value),
                'total' => $total,
                'pct' => $totalExpenses > 0 ? round($total / $totalExpenses * 100, 1) : 0,
                'count' => (int) ($row->count ?? 0),
            ];
        })->filter(fn ($c) => $c['total'] > 0)->values()->toArray();

        // Tendance 12 mois des dépenses (groupe par PÉRIODE métier 21→20)
        $startDay = getMonthPeriodStartDay();
        $trendPeriods = collect(range(0, 11))->map(fn ($i) => [
            'period' => $p = getPeriodFromDate(now()->subMonths($i)),
            'range' => getPeriodRange($p),
            'date' => \Carbon\Carbon::createFromFormat('!Y-m', $p),
        ]);
        $trendStart = $trendPeriods->min('range.start');
        $trendEnd = $trendPeriods->max('range.end');
        $periodTrendTotals = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$trendStart, $trendEnd])
            ->selectRaw($this->periodGroupExpression(), [$startDay])
            ->groupBy('period')
            ->pluck('total', 'period');
        $monthlyTrend = $trendPeriods->map(function ($tp) use ($periodTrendTotals) {
            $total = (float) ($periodTrendTotals[$tp['period']] ?? 0);
            return [
                'month' => $tp['date']->translatedFormat('M Y'),
                'total' => (float) $total,
            ];
        })->reverse()->values()->toArray();

        // Historique du déficit en caisse
        $deficitHistory = \App\Domains\Alerts\Models\Alert::whereIn('type', ['deficit_deducted', 'deficit_increased'])
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($a) => [
                'month' => formatPeriodLabel($a->data['closure_month'] ?? ''),
                'type' => $a->type,
                'amount' => (float) ($a->data['deduction'] ?? $a->data['increase'] ?? 0),
                'remaining' => (float) ($a->data['remaining'] ?? $a->data['new_total'] ?? 0),
                'date' => $a->created_at->format('d/m/Y H:i'),
            ])->toArray();

        // Historique des clôtures
        $closureHistory = \App\Domains\Treasury\Models\MonthlyClosure::with('closer')
            ->latest()
            ->take(12)
            ->get()
            ->map(fn ($c) => [
                'label' => formatPeriodLabel($c->month),
                'gains' => (float) $c->gains,
                'expenses' => (float) $c->expenses,
                'balance' => (float) $c->balance,
                'closed_by' => $c->closer?->name ?? '-',
                'date' => $c->created_at->format('d/m/Y H:i'),
            ])->toArray();

        // Taux de croissance des gains : MÊME règle que le dashboard (fix b874d8f)
        // — comparaison UNIQUEMENT entre périodes ADJACENTES, sinon null (le
        // graphique casse la ligne). Un mois non clôturé ne fabrique plus un
        // faux taux en deux boules.
        $closures = \App\Domains\Treasury\Models\MonthlyClosure::orderBy('month', 'asc')->get(['month', 'gains']);
        $growthTrend = [];
        $prevMonth = null;
        $prevGains = null;
        foreach ($closures as $c) {
            $contiguous = false;
            if ($prevMonth !== null) {
                $expected = \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->addMonthNoOverflow()->format('Y-m');
                $contiguous = ($c->month === $expected);
            }
            $rate = null;
            if ($contiguous && (float) $prevGains > 0) {
                $rate = round(((float) $c->gains - (float) $prevGains) / (float) $prevGains * 100, 1);
            }
            $growthTrend[] = [
                'label' => formatPeriodLabel($c->month),
                'rate' => $rate,
            ];
            $prevMonth = $c->month;
            $prevGains = (float) $c->gains;
        }

        // Entrées d'argent (par source)
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
        $incomeTotal = (float) \App\Domains\Treasury\Models\Income::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $incomeByTypeLabels = [];
        $incomeByTypeValues = [];
        $incomeByTypeColors = [];
        foreach ($incomeTypeOrder as $i => $key) {
            $total = (float) ($incomeTotals->get($key)->total ?? 0);
            if ($total <= 0) continue;
            $incomeByTypeLabels[] = $incomeTypeLabels[$key];
            $incomeByTypeValues[] = $total;
            $incomeByTypeColors[] = $incomeColors[$i];
        }

        // Tendance 12 mois des entrées — FIX cohérive: maintenant groupée par PÉRIODE
        // métier (21→20, même CASE que les dépenses) au lieu du mois calendaire :
        // les deux courbes partagen un axe comparable.
        $incomeTrendTotals = \App\Domains\Treasury\Models\Income::whereBetween('date', [$trendStart, $trendEnd])
            ->selectRaw($this->periodGroupExpression(), [$startDay])
            ->groupBy('period')
            ->pluck('total', 'period');
        $incomeTrend = $trendPeriods->map(function ($tp) use ($incomeTrendTotals) {
            $total = (float) ($incomeTrendTotals[$tp['period']] ?? 0);
            return [
                'month' => $tp['date']->translatedFormat('M Y'),
                'total' => $total,
            ];
        })->reverse()->values()->toArray();

        // === Commissions stations (nouveau module) ===
        $scRows = \App\Domains\Treasury\Models\StationCharge::period($period)->get();
        $stationCharges = $scRows->map(fn ($c) => [
            'station' => $c->station_name,
            'amount' => (float) $c->amount,
            'paid' => $c->paid_at !== null,
        ])->sortBy('station')->values()->toArray();

        $dailyTotal = (float) \App\Domains\Expenses\Models\Expense::whereDate('date', today())->sum('amount');
        $cashDeficit = (float) \App\Domains\Settings\Models\Setting::get('cash_deficit', 0);

        return [
            'totalExpenses' => $totalExpenses,
            'expenseCount' => $expenseCount,
            'daysInPeriod' => $daysInPeriod,
            'averagePerDay' => round($totalExpenses / max(1, $daysInPeriod), 2),
            'previousExpenses' => $previousExpenses,
            'totalGains' => $closure ? (float) $closure->gains : 0,
            'balance' => $closure ? (float) $closure->balance : 0,
            'expensesByCategory' => $expensesByCategory,
            'repetitiveByCategory' => $repetitiveByCategory,
            'paymentMethodData' => $paymentMethodData,
            'monthlyTrend' => $monthlyTrend,
            'dailyTotal' => $dailyTotal,
            'cashDeficit' => $cashDeficit,
            'deficitHistory' => $deficitHistory,
            'closureHistory' => $closureHistory,
            'growthTrend' => $growthTrend,
            'incomeTotal' => $incomeTotal,
            'incomeByTypeLabels' => $incomeByTypeLabels,
            'incomeByTypeValues' => $incomeByTypeValues,
            'incomeByTypeColors' => $incomeByTypeColors,
            'incomeTrend' => $incomeTrend,
            'stationChargesTotal' => (float) collect($stationCharges)->sum('amount'),
            'stationPaidTotal' => (float) collect($stationCharges)->where('paid', true)->sum('amount'),
            'stationChargesRows' => $stationCharges,
        ];
    }

    public function exportExcel()
    {
        if (!$this->period) return;
        $period = $this->period;
        $this->notify(__('statistics.export_started', ['default' => 'Export Excel en cours…']));
        return Excel::download(new StatisticsPeriodExport($period), 'depenses-' . $period . '.xlsx');
    }

    public function exportCsv()
    {
        if (!$this->period) return;
        $period = $this->period;
        $this->notify(__('statistics.export_started', ['default' => 'Export CSV en cours…']));
        return Excel::download(new StatisticsPeriodExport($period), 'depenses-' . $period . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /** SQL de regroupement par PÉRIODE métier (21→20) — délégue à PeriodSql (source unique partagée Dashboard/Statistics). */
    private function periodGroupExpression(): string
    {
        $helper = \App\Shared\Support\PeriodSql::class;
        // PeriodSql expose le regroupement complet par requête ; expression extraite
        // en copiant la même logique inline pour compat ascendante des 2 appels existants.
        // ⚠️ Le placeholder ? DOIT rester : les 2 appels passent le binding [$startDay].
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            return "strftime('%Y-%m',
                CASE
                    WHEN CAST(strftime('%d', date) AS INT) > ? THEN date(date, '+1 month')
                    ELSE date
                END) as period,
            SUM(amount) as total";
        }
        return "DATE_FORMAT(
                CASE
                    WHEN DAY(date) > ? THEN DATE_ADD(date, INTERVAL 1 MONTH)
                    ELSE date
                END,
                '%Y-%m'
            ) as period,
            SUM(amount) as total";
    }

    private function categoryColor(int $id): string
    {
        return \App\Shared\Support\CategoryPalette::color($id);
    }
}
