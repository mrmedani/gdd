<?php

namespace App\Domains\Reports\Controllers;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Reports\Requests\ReportRequest;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Domains\Reports\Exports\ExpenseExport;
use App\Domains\Reports\Exports\ExpenseRangeExport;

class ReportController extends Controller
{
    /** 1re période métier de l'année (label Y-01, ouvre le 21/12 de l'année -1). */
    private function businessYearStart(string $yearMonth): string
    {
        return \Carbon\Carbon::createFromFormat('!Y-m', $yearMonth)->format('Y') . '-01';
    }

    private function applyFilters($query, array $data): void
    {
        \App\Domains\Reports\Support\ReportAggregator::applyFilters($query, $data);
    }

    public function monthlyPdf(ReportRequest $request)
    {
        $data = $request->validated();
        $yearMonth = sprintf('%04d-%02d', $data['year'], $data['month']);

        $expenses = Expense::with('category.parent', 'employee');
        $this->applyFilters($expenses, $data);
        $expenses = $expenses->byPeriod($yearMonth)->latest('date')->get();

        $total = $expenses->sum('amount');
        // SOURCE UNIQUE (mensuel/annuel/personnalisé)
        $byCategory = \App\Domains\Reports\Support\ReportAggregator::byCategory($expenses, $total)
            ->map(fn($items, $cat) => ['name' => $cat] + $items);
        $byPaymentMethod = \App\Domains\Reports\Support\ReportAggregator::byPaymentMethod($expenses);

        $closure = MonthlyClosure::where('month', $yearMonth)->first();
        $gains = $closure?->gains ?? 0;
        $balance = $closure?->balance ?? ($gains - $total);

        // Previous period comparison
        $prevDate = \Carbon\Carbon::createFromFormat('!Y-m', $yearMonth)->subMonth();
        $prevPeriod = getPeriodFromDate($prevDate);
        $prevExpenses = Expense::byPeriod($prevPeriod);
        $this->applyFilters($prevExpenses, $data);
        $prevTotal = $prevExpenses->sum('amount');
        $prevCount = $prevExpenses->count();
        $prevClosure = MonthlyClosure::where('month', $prevPeriod)->first();
        $prevGains = $prevClosure?->gains ?? 0;

        // Budget vs actual — FIX #4 : le budget peut être défini sur la catégorie PARENT
        // (l'ancien code regardait l'id feuille exact → budget jamais trouvé).
        // On déduit la clé de budget par ($category->parent_id ?: $category->id).
        $budgets = Setting::get('category_budgets', '{}');
        $budgets = is_string($budgets) ? json_decode($budgets, true) ?? [] : ($budgets ?? []);
        $budgetComparison = collect();
        $budgetKeyByCatName = [];
        foreach ($expenses as $e) {
            if (!$e->category) continue;
            $name = $e->category->parent?->translated_name ?? $e->category->translated_name ?? $e->category_key;
            $budgetKey = $e->category->parent_id ?: $e->category_id;
            // Le budget d'un budget parent s'applique aux dépenses de ses enfants
            $val = (float) ($budgets[$budgetKey] ?? 0);
            if ($val <= 0 && $e->category->parent_id) {
                $val = (float) ($budgets[$e->category_id] ?? 0);
            }
            $budgetKeyByCatName[$name] = max((float)($budgetKeyByCatName[$name] ?? 0), $val);
        }
        foreach ($byCategory as $catName => $catInfo) {
            $catActual = (float) $catInfo['total'];
            $catBudget = (float) ($budgetKeyByCatName[$catName] ?? 0);
            if ($catBudget > 0) {
                $budgetComparison->push([
                    'name' => $catName,
                    'budget' => $catBudget,
                    'actual' => $catActual,
                    'variance' => $catActual - $catBudget,
                    'variance_pct' => $catBudget > 0 ? round((($catActual - $catBudget) / $catBudget) * 100, 1) : 0,
                ]);
            }
        }

        // Top 10 expenses
        $topExpenses = $expenses->sortByDesc('amount')->take(10);

        $range = getPeriodRange($yearMonth);

        // YTD (année métier, cohérent avec les périodes 21→20) : de la période
        // "Y-01" (21/12 de l'année précédente → 20/01) jusqu'à la fin de la période affichée.
        $ytdStart = getPeriodRange($this->businessYearStart($yearMonth))['start'];
        $ytdEnd = $range;
        $ytdExpenses = Expense::whereBetween('date', [$ytdStart->toDateString(), $ytdEnd['end']->toDateString()]);
        $this->applyFilters($ytdExpenses, $data);
        $ytdTotal = $ytdExpenses->sum('amount');

        // Daily breakdown (délégation Aggregator)
        $dailyBreakdown = \App\Domains\Reports\Support\ReportAggregator::dailyBreakdown($expenses, $range['start'], $range['end']);
        $maxDaily = $dailyBreakdown->max('total');
        $busiestDay = $dailyBreakdown->sortByDesc('total')->first();
        $dailyAverage = $dailyBreakdown->count() > 0 ? $total / $dailyBreakdown->count() : 0;

        // Employee breakdown (délégation Aggregator)
        $byEmployee = \App\Domains\Reports\Support\ReportAggregator::byEmployee($expenses, $total);

        $company = [
            'name' => Setting::get('app_name', config('app.name')),
            'currency' => getCurrency(),
        ];

        $pdf = Pdf::loadView('reports.monthly', [
            'month' => $data['month'],
            'year' => $data['year'],
            'yearMonth' => $yearMonth,
            'expenses' => $expenses,
            'total' => $total,
            'gains' => $gains,
            'balance' => $balance,
            'byCategory' => $byCategory,
            'byPaymentMethod' => $byPaymentMethod,
            'prevTotal' => $prevTotal,
            'prevCount' => $prevCount,
            'prevGains' => $prevGains,
            'budgetComparison' => $budgetComparison,
            'topExpenses' => $topExpenses,
            'ytdTotal' => $ytdTotal,
            'dailyBreakdown' => $dailyBreakdown,
            'maxDaily' => $maxDaily,
            'busiestDay' => $busiestDay,
            'dailyAverage' => $dailyAverage,
            'byEmployee' => $byEmployee,
            'company' => $company,
            'periodLabel' => formatPeriodLabel($yearMonth),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("rapport-mensuel-{$data['month']}-{$data['year']}.pdf");
    }

    public function annualPdf(ReportRequest $request)
    {
        $data = $request->validated();

        $expenses = Expense::with('category.parent', 'employee');
        $this->applyFilters($expenses, $data);
        $expenses = $expenses->byYear($data['year'])->latest('date')->get();

        $total = $expenses->sum('amount');

        // FIX #2 : mensuel agrégé par PÉRIODE MÉTIER (découpage 21→20), cohérent avec
        // la clôture /statistics /dashboard. Clé de période = label (21/12/prev → 20/01).
        $periodTotals = [];
        $periodCounts = [];
        foreach ($expenses as $e) {
            $p = getPeriodFromDate($e->date);
            $periodTotals[$p] = ($periodTotals[$p] ?? 0) + (float) $e->amount;
            $periodCounts[$p] = ($periodCounts[$p] ?? 0) + 1;
        }
        $byMonth = collect(range(1, 12))->map(function ($m) use ($data, $periodTotals, $periodCounts) {
            $p = sprintf('%04d-%02d', $data['year'], $m);
            $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $p);
            return [
                'month' => $m,
                'label' => __($periodDate->startOfMonth()->format('F')),
                'total' => (float) ($periodTotals[$p] ?? 0),
                'count' => (int) ($periodCounts[$p] ?? 0),
            ];
        });

        $byCategory = \App\Domains\Reports\Support\ReportAggregator::byCategory($expenses, $total)
            ->map(fn($items, $cat) => ['name' => $cat] + $items);

        // Previous year comparison
        $prevYearExpenses = Expense::byYear($data['year'] - 1);
        $this->applyFilters($prevYearExpenses, $data);
        $prevTotal = $prevYearExpenses->sum('amount');

        $gains = MonthlyClosure::where('month', 'like', "{$data['year']}-%")->sum('gains');

        // Top 10 expenses
        $topExpenses = $expenses->sortByDesc('amount')->take(10);

        // Employee breakdown (Aggregator)
        $byEmployee = \App\Domains\Reports\Support\ReportAggregator::byEmployee($expenses, $total);

        // By payment method (Aggregator)
        $byPaymentMethod = \App\Domains\Reports\Support\ReportAggregator::byPaymentMethod($expenses);

        // Monthly average
        $activeMonths = $byMonth->filter(fn($m) => $m['count'] > 0)->count();
        $monthlyAvg = $activeMonths > 0 ? $total / $activeMonths : 0;

        $company = [
            'name' => Setting::get('app_name', config('app.name')),
            'currency' => getCurrency(),
        ];

        $pdf = Pdf::loadView('reports.annual', [
            'year' => $data['year'],
            'expenses' => $expenses,
            'total' => $total,
            'gains' => $gains,
            'balance' => $gains - $total,
            'byMonth' => $byMonth,
            'byCategory' => $byCategory,
            'prevTotal' => $prevTotal,
            'topExpenses' => $topExpenses,
            'byEmployee' => $byEmployee,
            'byPaymentMethod' => $byPaymentMethod,
            'monthlyAvg' => $monthlyAvg,
            'company' => $company,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("rapport-annuel-{$data['year']}.pdf");
    }

    public function monthlyExcel(ReportRequest $request)
    {
        $data = $request->validated();
        return Excel::download(
            new ExpenseExport($data['year'], $data['month'], 'monthly', $data),
            "depenses-{$data['month']}-{$data['year']}.xlsx"
        );
    }

    public function annualExcel(ReportRequest $request)
    {
        $data = $request->validated();
        return Excel::download(
            new ExpenseExport($data['year'], null, 'annual', $data),
            "depenses-{$data['year']}.xlsx"
        );
    }

    /**
     * FIX #6 : rapport personnalisé sur une PLAGE DE DATES libre (ex 18-25 sept)
     * avec les mêmes filtres. Utilise reports.custom.blade.php.
     */
    public function customPdf(ReportRequest $request)
    {
        $data = $request->validated();

        $start = \Carbon\Carbon::parse($data['date_from'])->startOfDay();
        $end = \Carbon\Carbon::parse($data['date_to'])->endOfDay();

        $expenses = Expense::with('category.parent', 'employee', 'creator')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->latest('date');
        $this->applyFilters($expenses, $data);
        $expenses = $expenses->get();

        $pdf = Pdf::loadView('reports.custom', \App\Domains\Reports\Support\ReportPdf::buildRangePayload($expenses, $start, $end));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download(__('reports.filename_custom', ['from' => $data['date_from'], 'to' => $data['date_to']]) . '.pdf');
    }

    public function customExcel(ReportRequest $request)
    {
        $data = $request->validated();

        $start = \Carbon\Carbon::parse($data['date_from'])->startOfDay();
        $end = \Carbon\Carbon::parse($data['date_to'])->endOfDay();

        $expenses = Expense::with('category.parent', 'creator', 'employee')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->latest('date');
        $this->applyFilters($expenses, $data);
        $rows = $expenses->get();
        return Excel::download(
            new ExpenseRangeExport($rows),
            __('reports.filename_custom', ['from' => $data['date_from'], 'to' => $data['date_to']]) . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /** FIX #8 : envoi du rapport (PDF) par WhatsApp au compte utilisateur connecté. */
    public function shareWhatsApp(ReportRequest $request)
    {
        $data = $request->validated();
        $kind = in_array($data['kind'] ?? '', ['monthly', 'annual', 'custom']) ? $data['kind'] : null;
        if (!$kind) {
            return response()->json(['ok' => false, 'error' => 'kind invalid'], 400);
        }
        $result = \App\Domains\Reports\Support\ReportPdf::shareWhatsApp(auth()->id(), $kind, $data);
        return response()->json($result, $result['ok'] ? 200 : 400);
    }
}
