<?php

namespace App\Domains\Expenses\Livewire;

use App\Domains\Employees\Models\Employee;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Shared\Enums\PaymentMethod;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Shared\Livewire\WithToast;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExpenseForm extends Component
{
    use WithFileUploads, WithToast;

    public ?int $expenseId = null;
    public string $date = '';
    public string $amount = '';
    public string $category_id = '';
    public string $description = '';
    public string $payment_method = '';
    public $receipt = null;
    public string $notes = '';
    public string $employee_id = '';
    public string $existingReceipt = '';
    public string $employeeAdvanceInfo = '';
    public bool $showEmployeeAdvanceInfo = false;
    public string $sub_category_id = '';
    public array $subCategories = [];
    public bool $showSubCategoryField = false;

    protected function rules(): array
    {
        $rules = [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category_id' => 'nullable|exists:expense_categories,id',
            'description' => 'required|string|max:500',
            'payment_method' => 'required|in:' . implode(',', array_column(PaymentMethod::cases(), 'value')),
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->isSalaryCategory()) {
            $rules['employee_id'] = 'required|exists:employees,id';
        }

        return $rules;
    }

    protected $messages = [
        'date.required' => 'validation.date_required',
        'amount.required' => 'validation.amount_required',
        'amount.min' => 'validation.amount_min',
        'category_id.required' => 'validation.category_required',
        'description.required' => 'validation.description_required',
        'receipt.max' => 'validation.receipt_max',
    ];

    public function mount(?Expense $expense = null): void
    {
        if ($expense?->exists) {
            Gate::authorize('update', $expense);
            $this->expenseId = $expense->id;
            $this->date = $expense->date->format('Y-m-d');
            $this->amount = (string) $expense->amount;
            $this->description = $expense->description;
            $this->payment_method = $expense->payment_method;
            $this->existingReceipt = $expense->receipt_path ?? '';
            $this->notes = $expense->notes ?? '';
            $this->employee_id = (string) ($expense->employee_id ?? '');

            $cat = $expense->category;
            if ($cat && $cat->parent_id) {
                $this->category_id = (string) $cat->parent_id;
                $this->sub_category_id = (string) $cat->id;
                $this->subCategories = $cat->parent->children()->where('is_active', true)->get()->toArray();
                $this->showSubCategoryField = true;
            } else {
                $this->category_id = (string) $expense->category_id;
            }
        } else {
            $this->date = now()->format('Y-m-d');
            $this->payment_method = 'cash';
        }
    }

    public function save(): void
    {
        // Nettoyer le montant avant validation
        if (is_string($this->amount)) {
            $this->amount = str_replace(',', '.', $this->amount);
        }

        $this->validate();

        $period = getPeriodFromDate($this->date);
        if (\App\Domains\Treasury\Models\MonthlyClosure::where('month', $period)->exists()) {
            return;
        }

        $finalCategoryId = $this->sub_category_id ?: $this->category_id;
        $category = ExpenseCategory::findOrFail($finalCategoryId);

        $data = [
            'date' => $this->date,
            'amount' => $this->amount,
            'category_id' => $category->id,
            'category_key' => $category->key ?? $this->getCategoryKey($category->id),
            'description' => $this->description,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'employee_id' => $this->isSalaryCategory() ? $this->employee_id : null,
        ];

        if ($this->receipt) {
            if ($this->expenseId) {
                $expense = Expense::find($this->expenseId);
                if ($expense && $expense->receipt_path) {
                    Storage::disk('public')->delete($expense->receipt_path);
                }
            }
            $data['receipt_path'] = $this->receipt->store('receipts', 'public');
        } elseif ($this->expenseId) {
            unset($data['receipt_path']);
        }

        if ($this->expenseId) {
            $expense = Expense::findOrFail($this->expenseId);
            Gate::authorize('update', $expense);
            $expense->update($data);
        } else {
            Gate::authorize('create', Expense::class);
            $data['created_by'] = auth()->id();
            Expense::create($data);
        }

        $this->notify($this->expenseId ? __('common.updated') : __('common.created'));
        $this->redirect(route('expenses.index'), navigate: false);
    }

    public function removeReceipt(): void
    {
        $this->receipt = null;
    }

    public function updatedCategoryId(): void
    {
        $category = ExpenseCategory::with('children')->find($this->category_id);
        if ($category && $category->children->where('is_active', true)->isNotEmpty()) {
            $this->subCategories = $category->children->where('is_active', true)->values()->toArray();
            $this->showSubCategoryField = true;
            $this->sub_category_id = '';
        } else {
            $this->subCategories = [];
            $this->showSubCategoryField = false;
            $this->sub_category_id = '';
        }

        if (!$this->isSalaryCategory()) {
            $this->employee_id = '';
            $this->showEmployeeAdvanceInfo = false;
            $this->employeeAdvanceInfo = '';
        }
        $this->resetValidation('employee_id');
    }

    public function updatedEmployeeId(): void
    {
        if (!$this->employee_id || !$this->isSalaryCategory()) {
            $this->showEmployeeAdvanceInfo = false;
            $this->employeeAdvanceInfo = '';
            return;
        }

        $employee = Employee::find($this->employee_id);

        if (!$employee) {
            $this->showEmployeeAdvanceInfo = false;
            $this->employeeAdvanceInfo = '';
            return;
        }

        $totalAdvances = $employee->advances()->whereIn('status', ['pending', 'approved'])->sum('amount');

        if ($totalAdvances > 0) {
            $this->employeeAdvanceInfo = (string) $totalAdvances;
            $this->showEmployeeAdvanceInfo = true;
        } else {
            $this->showEmployeeAdvanceInfo = false;
            $this->employeeAdvanceInfo = (string) $totalAdvances;
        }
    }

    public function render()
    {
        $categories = ExpenseCategory::active()->whereNull('parent_id')->get();
        $paymentMethods = PaymentMethod::options();
        $showEmployeeField = $this->isSalaryCategory();
        $employees = $showEmployeeField
            ? Employee::where('status', 'active')->get()
            : collect();

        $pageTitle = $this->expenseId ? __('expenses.edit') : __('expenses.add');

        return view('livewire.expense-form', [
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'employees' => $employees,
            'showEmployeeField' => $showEmployeeField,
        ])->layout('layouts.app')->title($pageTitle);
    }

    private function isSalaryCategory(): bool
    {
        $category = ExpenseCategory::find($this->category_id);
        return $category && $category->key === 'salaries';
    }

    private function getCategoryKey(int $id): string
    {
        $map = [
            1 => 'salaries', 2 => 'fuel', 3 => 'rent',
            4 => 'internet', 5 => 'electricity', 6 => 'vehicle_maintenance',
            7 => 'supplies', 8 => 'advertising', 9 => 'other',
        ];
        return $map[$id] ?? 'other';
    }
}
