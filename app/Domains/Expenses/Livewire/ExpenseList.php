<?php

namespace App\Domains\Expenses\Livewire;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Shared\Enums\PaymentMethod;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ExpenseList extends Component
{
    use WithToast;
    use WithPagination;
    use WithFileUploads;

    public string $searchDateFrom = '';
    public string $searchDateTo = '';
    public string $searchCategory = '';
    public string $searchAmountMin = '';
    public string $searchAmountMax = '';
    public string $searchPaymentMethod = '';

    // Bulk actions
    public array $selectedExpenses = [];
    public bool $selectAll = false;

    // Import
    public bool $showImportModal = false;
    public bool $showImportPreview = false;
    public $importFile = null;
    public array $importPreviewRows = [];
    public string $importLog = '';

    protected $queryString = [
        'searchDateFrom' => ['except' => ''],
        'searchDateTo' => ['except' => ''],
        'searchCategory' => ['except' => ''],
        'searchAmountMin' => ['except' => ''],
        'searchAmountMax' => ['except' => ''],
        'searchPaymentMethod' => ['except' => ''],
    ];

    public function updated(string $property, mixed $value): void
    {
        if (str_starts_with($property, 'search')) {
            $this->resetPage();
        }
    }

    public function delete(int $id): void
    {
        $expense = Expense::findOrFail($id);
        Gate::authorize('delete', $expense);

        $period = getPeriodFromDate($expense->date);
        if (\App\Domains\Treasury\Models\MonthlyClosure::where('month', $period)->exists()) {
            return;
        }

        $expense->delete();
        $this->notify(__('common.deleted'));
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedExpenses = $value
            ? $this->getFilteredQuery()->latest()->pluck('id')->toArray()
            : [];
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedExpenses)) return;

        Gate::authorize('delete', Expense::class);

        $expenses = Expense::whereIn('id', $this->selectedExpenses)->get();

        foreach ($expenses as $expense) {
            $period = getPeriodFromDate($expense->date);
            if (\App\Domains\Treasury\Models\MonthlyClosure::where('month', $period)->exists()) {
                return;
            }
        }

        Expense::whereIn('id', $this->selectedExpenses)->delete();
        $this->selectedExpenses = [];
        $this->selectAll = false;
        $this->notify(__('common.deleted'));
    }

    public function getFilteredQuery()
    {
        $query = Expense::with('category', 'creator', 'employee');

        if ($this->searchDateFrom) {
            $query->whereDate('date', '>=', $this->searchDateFrom);
        }
        if ($this->searchDateTo) {
            $query->whereDate('date', '<=', $this->searchDateTo);
        }
        if ($this->searchCategory) {
            $query->where('category_id', $this->searchCategory);
        }
        if ($this->searchAmountMin !== '') {
            $query->where('amount', '>=', (float) $this->searchAmountMin);
        }
        if ($this->searchAmountMax !== '') {
            $query->where('amount', '<=', (float) $this->searchAmountMax);
        }
        if ($this->searchPaymentMethod) {
            $query->where('payment_method', $this->searchPaymentMethod);
        }

        return $query;
    }

    public function exportCsv()
    {
        Gate::authorize('create', Expense::class);

        $expenses = $this->getFilteredQuery()->latest()->get();

        $fileName = 'expenses-' . now()->format('Y-m-d-His') . '.csv';
        $tempPath = storage_path('app/temp/' . $fileName);

        $dir = dirname($tempPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $handle = fopen($tempPath, 'w');
        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'Date', 'Description', 'Category', 'Amount', 'Payment Method', 'Employee', 'Notes', 'Created By'
        ]);

        foreach ($expenses as $expense) {
            fputcsv($handle, [
                $expense->date->format('Y-m-d'),
                $expense->description,
                $expense->category?->translated_name ?? $expense->category_key,
                (float) $expense->amount,
                $expense->payment_method,
                $expense->employee?->name ?? '',
                $expense->notes ?? '',
                $expense->creator?->name ?? '-',
            ]);
        }

        fclose($handle);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function previewCsv(): void
    {
        Gate::authorize('create', Expense::class);

        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->importFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))), $header);

        $expected = ['date', 'description', 'amount'];
        $missing = array_diff($expected, $header);
        if (count($missing) > 0) {
            fclose($handle);
            return;
        }

        $this->importPreviewRows = [];
        $categoryCache = [];
        $rowNum = 0;

        while (($row = fgetcsv($handle)) !== false && $rowNum < 20) {
            $rowNum++;
            try {
                $data = array_combine($header, $row);
                $date = trim($data['date'] ?? '');
                $description = trim($data['description'] ?? '');
                $amount = str_replace(',', '.', trim($data['amount'] ?? '0'));

                if (empty($date) || empty($description) || (float) $amount <= 0) continue;

                $categoryKey = trim($data['category'] ?? 'other');
                if (!isset($categoryCache[$categoryKey])) {
                    $cat = ExpenseCategory::where('key', $categoryKey)->orWhere('name_fr', $categoryKey)->first();
                    $categoryCache[$categoryKey] = $cat?->id ?? ExpenseCategory::where('key', 'other')->first()?->id ?? 1;
                }

                $this->importPreviewRows[] = [
                    'date' => $date,
                    'description' => $description,
                    'amount' => (float) $amount,
                    'category' => $categoryKey,
                ];
            } catch (\Throwable) {
                continue;
            }
        }

        fclose($handle);
        $this->showImportPreview = !empty($this->importPreviewRows);
    }

    public function confirmImport(): void
    {
        Gate::authorize('create', Expense::class);

        $path = $this->importFile->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))), $header);

        $imported = 0;
        $errors = 0;
        $categoryCache = [];
        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);
                $date = trim($data['date'] ?? '');
                $description = trim($data['description'] ?? '');
                $amount = str_replace(',', '.', trim($data['amount'] ?? '0'));

                if (empty($date) || empty($description) || (float) $amount <= 0) {
                    $errors++;
                    continue;
                }

                $categoryKey = trim($data['category'] ?? 'other');
                if (!isset($categoryCache[$categoryKey])) {
                    $cat = ExpenseCategory::where('key', $categoryKey)->orWhere('name_fr', $categoryKey)->first();
                    $categoryCache[$categoryKey] = $cat->id ?? 1;
                }

                $paymentMethod = trim($data['payment_method'] ?? 'cash');
                $validMethods = array_column(PaymentMethod::cases(), 'value');
                if (!in_array($paymentMethod, $validMethods)) $paymentMethod = 'cash';

                $rows[] = [
                    'date' => $date,
                    'description' => $description,
                    'amount' => (float) $amount,
                    'category_id' => $categoryCache[$categoryKey],
                    'category_key' => $categoryKey,
                    'payment_method' => $paymentMethod,
                    'notes' => trim($data['notes'] ?? ''),
                    'created_by' => auth()->id(),
                ];
            } catch (\Throwable) {
                $errors++;
            }
        }

        fclose($handle);

        DB::transaction(function () use ($rows, &$imported) {
            foreach ($rows as $row) {
                Expense::create($row);
                $imported++;
            }
        });

        $this->showImportModal = false;
        $this->showImportPreview = false;
        $this->importFile = null;
        $this->importPreviewRows = [];

    }

    public function cancelImport(): void
    {
        $this->showImportPreview = false;
        $this->importPreviewRows = [];
        $this->importFile = null;
    }

    public function render()
    {
        $expenses = $this->getFilteredQuery()->latest()->paginate(15);
        $categories = ExpenseCategory::active()->get();
        $paymentMethods = PaymentMethod::options();

        return view('livewire.expense-list', [
            'expenses' => $expenses,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
        ])->layout('layouts.app')->title(__('nav.expenses'));
    }
}
