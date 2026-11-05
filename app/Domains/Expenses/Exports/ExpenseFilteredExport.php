<?php

namespace App\Domains\Expenses\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exportué dépenses (page /expenses) : une collection déjà FILTRÉE par le composant
 * (les mêmes requêtes que le tableau) → Excel (xlsx) OU CSV via maatwebsite.
 * Entêtes i18n (fr/ar/en) — l'ancien export CSV était codé en texte anglais fixe.
 */
class ExpenseFilteredExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Collection $expenses) {}

    public function collection(): Collection
    {
        return $this->expenses;
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
