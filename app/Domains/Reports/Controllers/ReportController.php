<?php

namespace App\Domains\Reports\Controllers;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Reports\Requests\ReportRequest;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Domains\Reports\Exports\ExpenseExport;

class ReportController extends Controller
{
    public function monthlyPdf(ReportRequest $request)
    {
        $data = $request->validated();
        $yearMonth = sprintf('%04d-%02d', $data['year'], $data['month']);

        $expenses = Expense::with('category.parent', 'employee')
            ->byPeriod($yearMonth)
            ->latest('date')
            ->get();

        $total = $expenses->sum('amount');
        $byCategory = $expenses->groupBy(fn($e) => $e->category?->parent?->translated_name ?? $e->category?->translated_name ?? __("categories.{$e->category_key}"));
        $byPaymentMethod = $expenses->groupBy('payment_method')
            ->map(fn($items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);

        $closure = MonthlyClosure::where('month', $yearMonth)->first();
        $gains = $closure?->gains ?? 0;
        $balance = $closure?->balance ?? ($gains - $total);

        $company = [
            'name' => \App\Domains\Settings\Models\Setting::get('app_name', config('app.name')),
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
            'company' => $company,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->download("rapport-mensuel-{$data['month']}-{$data['year']}.pdf");
    }

    public function annualPdf(ReportRequest $request)
    {
        $data = $request->validated();

        $expenses = Expense::with('category.parent', 'employee')
            ->byYear($data['year'])
            ->latest('date')
            ->get();

        $total = $expenses->sum('amount');
        $byMonth = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'label' => now()->month($m)->translatedFormat('F'),
            'total' => $expenses->filter(fn($e) => (int) $e->date->month === $m)->sum('amount'),
            'count' => $expenses->filter(fn($e) => (int) $e->date->month === $m)->count(),
        ]);

        $byCategory = $expenses->groupBy(fn($e) => $e->category?->parent?->translated_name ?? $e->category?->translated_name ?? __("categories.{$e->category_key}"))
            ->map(fn($items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ]);

        $gains = MonthlyClosure::where('month', 'like', "{$data['year']}-%")->sum('gains');

        $company = [
            'name' => \App\Domains\Settings\Models\Setting::get('app_name', config('app.name')),
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
            'company' => $company,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->download("rapport-annuel-{$data['year']}.pdf");
    }

    public function monthlyExcel(ReportRequest $request)
    {
        $data = $request->validated();
        return Excel::download(
            new ExpenseExport($data['year'], $data['month'], 'monthly'),
            "depenses-{$data['month']}-{$data['year']}.xlsx"
        );
    }

    public function annualExcel(ReportRequest $request)
    {
        $data = $request->validated();
        return Excel::download(
            new ExpenseExport($data['year'], null, 'annual'),
            "depenses-{$data['year']}.xlsx"
        );
    }
}
