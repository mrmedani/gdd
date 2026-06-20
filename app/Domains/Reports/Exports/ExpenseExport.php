<?php

namespace App\Domains\Reports\Exports;

use App\Domains\Expenses\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private int $year,
        private ?int $month = null,
        private string $type = 'monthly',
    ) {}

    public function collection(): Collection
    {
        $query = Expense::with('category', 'creator', 'employee');

        if ($this->type === 'monthly' && $this->month) {
            $yearMonth = sprintf('%04d-%02d', $this->year, $this->month);
            $query->byPeriod($yearMonth);
        } else {
            $query->byYear($this->year);
        }

        return $query->latest('date')->get();
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
