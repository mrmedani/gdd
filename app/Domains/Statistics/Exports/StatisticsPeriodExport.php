<?php

namespace App\Domains\Statistics\Exports;

use App\Domains\Expenses\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export du TABLEAU de dépenses d'une période donnée (21→20), avec les mêmes
 * colonnes que la pagination de la page /statistics. Livewire 3 supporte le
 * retour direct Excel::download(...) d'une méthode Livewire.
 */
class StatisticsPeriodExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private string $period) {}

    public function collection(): Collection
    {
        $range = getPeriodRange($this->period);
        return Expense::with('category', 'creator', 'employee')
            ->whereBetween('date', [$range['start'], $range['end']])
            ->latest('date')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('expenses.date'),
            __('expenses.description'),
            __('expenses.category'),
            __('expenses.amount'),
            __('expenses.payment_method'),
            __('expenses.employee'),
            __('expenses.notes'),
            __('common.created_by'),
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date->format('Y-m-d'),
            $expense->description,
            $expense->category?->translated_name ?? $expense->category_key,
            (float) $expense->amount,
            __("payment_methods.{$expense->payment_method}"),
            $expense->employee?->name ?? '',
            $expense->notes ?? '',
            $expense->creator?->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
