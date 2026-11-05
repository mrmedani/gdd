<?php

namespace App\Domains\Reports\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export des dépenses d'une PLAGue de dates personnalisée (rapport
 * « range personnalisé » /reports + envoi WhatsApp). Entêtes i18n —
 * réutilise le même format que ExpenseFilteredExport (page /expenses).
 */
class ExpenseRangeExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
