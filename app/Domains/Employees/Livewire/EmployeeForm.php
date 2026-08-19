<?php

namespace App\Domains\Employees\Livewire;

use App\Domains\Employees\Models\Employee;
use App\Domains\Employees\Models\SalaryAdvance;
use App\Domains\Employees\Models\SalaryPayment;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class EmployeeForm extends Component
{
    use WithToast;

    public ?int $employeeId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role_title = '';
    public string $base_salary = '';
    public string $hired_at = '';
    public string $status = 'active';

    public $activeTab = 'details';

    // Payment & Advance properties
    public string $advanceAmount = '';
    public string $advanceDate = '';
    public string $advanceNotes = '';

    public string $paymentMonth = '';
    public string $paymentYear = '';
    public string $paymentDeduction = '0';
    public string $paymentMethod = 'cash';
    public string $paymentNotes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role_title' => 'nullable|string|max:255',
            'base_salary' => 'required|numeric|min:0',
            'hired_at' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function mount(?Employee $employee = null)
    {
        Gate::authorize('manage-employees');
        if ($employee?->exists) {
            $this->employeeId = $employee->id;
            $this->name = $employee->name;
            $this->email = $employee->email ?? '';
            $this->phone = $employee->phone ?? '';
            $this->role_title = $employee->role_title ?? '';
            $this->base_salary = (string) $employee->base_salary;
            $this->hired_at = $employee->hired_at ? $employee->hired_at->format('Y-m-d') : '';
            $this->status = $employee->status;
        } else {
            $this->hired_at = now()->format('Y-m-d');
        }

        $this->advanceDate = now()->format('Y-m-d');
        $this->paymentMonth = now()->format('m');
        $this->paymentYear = now()->format('Y');
    }

    public function save()
    {
        if (is_string($this->base_salary)) {
            $this->base_salary = str_replace(',', '.', $this->base_salary);
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role_title' => $this->role_title,
            'base_salary' => $this->base_salary,
            'hired_at' => $this->hired_at,
            'status' => $this->status,
        ];

        if ($this->employeeId) {
            $employee = Employee::findOrFail($this->employeeId);
            $employee->update($data);
            $this->notify(__('common.updated'));
            $this->redirect(route('employees.index'), navigate: false);
        } else {
            $data['created_by'] = auth()->id();
            $employee = Employee::create($data);
            $this->notify(__('common.created'));
            $this->redirect(route('employees.edit', $employee->id), navigate: false);
        }
    }

    public function createAdvance()
    {
        Gate::authorize('manage-employees');

        $this->validate([
            'advanceAmount' => 'required|numeric|min:1',
            'advanceDate' => 'required|date',
        ]);

        $employee = Employee::findOrFail($this->employeeId);

        $category = ExpenseCategory::where('key', 'salaries')->first();

        Expense::$skipSalaryAdvanceDeduction = true;
        $expense = Expense::create([
            'date' => $this->advanceDate,
            'amount' => $this->advanceAmount,
            'category_id' => $category?->id,
            'category_key' => 'salaries',
            'description' => __('employees.advance_added') . ': ' . $employee->name,
            'payment_method' => 'cash',
            'notes' => $this->advanceNotes,
            'employee_id' => $this->employeeId,
            'created_by' => auth()->id(),
        ]);
        Expense::$skipSalaryAdvanceDeduction = false;

        $advance = SalaryAdvance::create([
            'employee_id' => $this->employeeId,
            'amount' => $this->advanceAmount,
            'date' => $this->advanceDate,
            'status' => 'approved',
            'notes' => $this->advanceNotes,
            'created_by' => auth()->id(),
            'expense_id' => $expense->id,
        ]);

        $this->advanceAmount = '';
        $this->advanceNotes = '';
        $this->notify(__('common.created'));
    }

    public function createPayment()
    {
        Gate::authorize('manage-employees');

        $this->validate([
            'paymentMonth' => 'required|numeric|min:1|max:12',
            'paymentYear' => 'required|numeric|min:2000',
            'paymentDeduction' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($this->employeeId);
        $net = $employee->base_salary - (float)$this->paymentDeduction;

        $payment = SalaryPayment::create([
            'employee_id' => $this->employeeId,
            'month' => $this->paymentMonth,
            'year' => $this->paymentYear,
            'base_amount' => $employee->base_salary,
            'advances_deducted' => $this->paymentDeduction,
            'net_amount' => $net,
            'payment_method' => $this->paymentMethod,
            'transaction_reference' => $this->paymentNotes,
            'paid_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // If deduction was applied, mark pending advances as deducted
        if ((float)$this->paymentDeduction > 0) {
            $deductionLeft = (float)$this->paymentDeduction;
            $advances = SalaryAdvance::where('employee_id', $this->employeeId)
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('date', 'asc')
                ->get();

            foreach ($advances as $adv) {
                if ($deductionLeft <= 0) break;
                
                if ($adv->amount <= $deductionLeft) {
                    $adv->update(['status' => 'deducted']);
                    $deductionLeft -= $adv->amount;
                }
            }
        }

        $this->paymentDeduction = '0';
        $this->paymentNotes = '';
        
        if ($net > 0) {
            $category = ExpenseCategory::where('key', 'salaries')->first();
            $expense = Expense::create([
                'date' => now()->format('Y-m-d'),
                'amount' => $net,
                'category_id' => $category?->id,
                'category_key' => 'salaries',
                'description' => __('employees.log_payment') . ' (' . $this->paymentMonth . '/' . $this->paymentYear . '): ' . $employee->name,
                'payment_method' => $this->paymentMethod,
                'notes' => $this->paymentNotes,
                'employee_id' => $this->employeeId,
                'created_by' => auth()->id(),
            ]);
            $payment->update(['expense_id' => $expense->id]);
        }

        $this->notify(__('common.created'));
    }

    public function deleteAdvance(int $id)
    {
        Gate::authorize('manage-employees');

        $advance = SalaryAdvance::findOrFail($id);

        if ($advance->expense_id) {
            $expense = Expense::find($advance->expense_id);
            if ($expense) {
                try {
                    $expense->delete();
                } catch (\Exception $e) {
                    $advance->expense_id = null;
                    $advance->save();
                }
            }
        }

        $advance->delete();
        $this->notify(__('common.deleted'));
    }

    public function deletePayment(int $id)
    {
        Gate::authorize('manage-employees');

        $payment = SalaryPayment::findOrFail($id);

        if ($payment->expense_id) {
            $expense = Expense::find($payment->expense_id);
            if ($expense) {
                try {
                    $expense->delete();
                } catch (\Exception $e) {
                    $payment->expense_id = null;
                    $payment->save();
                }
            }
        }

        $payment->delete();
        $this->notify(__('common.deleted'));
    }

    public function render()
    {
        $employee = $this->employeeId ? Employee::with(['advances', 'payments'])->findOrFail($this->employeeId) : null;
        
        return view('livewire.employee-form', [
            'employeeRecord' => $employee,
        ])->layout('layouts.app')->title($this->employeeId ? (__('employees.edit') ?? 'Edit Employee') : (__('employees.add') ?? 'Add Employee'));
    }
}
