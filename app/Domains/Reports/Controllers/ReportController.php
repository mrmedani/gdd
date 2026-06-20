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

class ReportController extends Controller
{
    private function applyFilters($query, array $data): void
    {
        if (!empty($data['category_id'])) {
            $ids = ExpenseCategory::where('id', $data['category_id'])
                ->orWhere('parent_id', $data['category_id'])
                ->pluck('id');
            $query->whereIn('category_id', $ids);
        }
        if (!empty($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }
        if (!empty($data['payment_method'])) {
            $query->where('payment_method', $data['payment_method']);
        }
    }

    public function monthlyPdf(ReportRequest $request)
    {
        $data = $request->validated();
        $yearMonth = sprintf('%04d-%02d', $data['year'], $data['month']);

        $expenses = Expense::with('category.parent', 'employee');
        $this->applyFilters($expenses, $data);
        $expenses = $expenses->byPeriod($yearMonth)->latest('date')->get();

        $total = $expenses->sum('amount');
        $byCategory = $expenses->groupBy(fn($e) => $e->category?->parent?->translated_name ?? $e->category?->translated_name ?? __("categories.{$e->category_key}"))
            ->map(fn($items, $cat) => [
                'name' => $cat,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])->sortByDesc('total');

        $byPaymentMethod = $expenses->groupBy('payment_method')
            ->map(fn($items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);

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

        // Budget vs actual
        $budgets = Setting::get('category_budgets', '{}');
        $budgets = is_string($budgets) ? json_decode($budgets, true) ?? [] : ($budgets ?? []);
        $budgetComparison = collect();
        foreach ($byCategory as $catName => $catInfo) {
            $catActual = $catInfo['total'];
            $catBudget = 0;
            foreach ($expenses->where(fn($e) => ($e->category?->parent?->translated_name ?? $e->category?->translated_name ?? $e->category_key) === $catName) as $e) {
                $catBudget += (float) ($budgets[$e->category_id] ?? 0);
            }
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

        // YTD cumulative
        $ytdStart = \Carbon\Carbon::createFromDate($data['year'], 1, 1);
        $ytdExpenses = Expense::whereDate('date', '>=', $ytdStart)->whereDate('date', '<=', now());
        $this->applyFilters($ytdExpenses, $data);
        $ytdTotal = $ytdExpenses->sum('amount');

        // Daily breakdown (spending per day in period)
        $range = getPeriodRange($yearMonth);
        $dailyTotals = $expenses->groupBy(fn($e) => $e->date->format('Y-m-d'))
            ->map(fn($items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);
        $dailyBreakdown = collect();
        $periodDays = $range['start']->copy();
        while ($periodDays->lte($range['end'])) {
            $key = $periodDays->format('Y-m-d');
            $dailyBreakdown->push([
                'date' => $periodDays->copy(),
                'label' => $periodDays->translatedFormat('D d'),
                'total' => $dailyTotals[$key]['total'] ?? 0,
                'count' => $dailyTotals[$key]['count'] ?? 0,
            ]);
            $periodDays->addDay();
        }
        $maxDaily = $dailyBreakdown->max('total');
        $busiestDay = $dailyBreakdown->sortByDesc('total')->first();
        $dailyAverage = $dailyBreakdown->count() > 0 ? $total / $dailyBreakdown->count() : 0;

        // Employee breakdown
        $byEmployee = $expenses->groupBy(fn($e) => $e->employee?->name ?? __('expenses.no_employee'))
            ->map(fn($items, $emp) => [
                'name' => $emp,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])->sortByDesc('total');

        $company = [
            'name' => Setting::get('app_name', config('app.name')),
            'currency' => getCurrency(),
        ];

        $html = view('reports.monthly', [
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
        ])->render();

        $html = \App\Shared\Helpers\ArabicPdfHelper::processHtml($html);
        $pdf = Pdf::loadHTML($html);

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->download("rapport-mensuel-{$data['month']}-{$data['year']}.pdf");
    }

    public function annualPdf(ReportRequest $request)
    {
        $data = $request->validated();

        $expenses = Expense::with('category.parent', 'employee');
        $this->applyFilters($expenses, $data);
        $expenses = $expenses->byYear($data['year'])->latest('date')->get();

        $total = $expenses->sum('amount');
        $byMonth = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'label' => now()->month($m)->translatedFormat('F'),
            'total' => $expenses->filter(fn($e) => (int) $e->date->month === $m)->sum('amount'),
            'count' => $expenses->filter(fn($e) => (int) $e->date->month === $m)->count(),
        ]);

        $byCategory = $expenses->groupBy(fn($e) => $e->category?->parent?->translated_name ?? $e->category?->translated_name ?? __("categories.{$e->category_key}"))
            ->map(fn($items) => [
                'name' => $items->first()->category?->parent?->translated_name ?? $items->first()->category?->translated_name ?? '',
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])->sortByDesc('total');

        // Previous year comparison
        $prevYearExpenses = Expense::byYear($data['year'] - 1);
        $this->applyFilters($prevYearExpenses, $data);
        $prevTotal = $prevYearExpenses->sum('amount');

        $gains = MonthlyClosure::where('month', 'like', "{$data['year']}-%")->sum('gains');

        // Top 10 expenses
        $topExpenses = $expenses->sortByDesc('amount')->take(10);

        // Employee breakdown
        $byEmployee = $expenses->groupBy(fn($e) => $e->employee?->name ?? __('expenses.no_employee'))
            ->map(fn($items, $emp) => [
                'name' => $emp,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])->sortByDesc('total');

        // By payment method
        $byPaymentMethod = $expenses->groupBy('payment_method')
            ->map(fn($items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);

        // Monthly average
        $activeMonths = $byMonth->filter(fn($m) => $m['count'] > 0)->count();
        $monthlyAvg = $activeMonths > 0 ? $total / $activeMonths : 0;

        $company = [
            'name' => Setting::get('app_name', config('app.name')),
            'currency' => getCurrency(),
        ];

        $html = view('reports.annual', [
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
        ])->render();

        $html = \App\Shared\Helpers\ArabicPdfHelper::processHtml($html);
        $pdf = Pdf::loadHTML($html);

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

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
}
