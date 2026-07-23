<?php

namespace App\Domains\Employees\Livewire;

use App\Domains\Employees\Models\Employee;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeList extends Component
{
    use WithPagination, WithToast;

    public string $searchName = '';
    public string $searchStatus = '';

    protected $queryString = [
        'searchName' => ['except' => ''],
        'searchStatus' => ['except' => ''],
    ];

    public function updatedSearchName()
    {
        $this->resetPage();
    }

    public function updatedSearchStatus()
    {
        $this->resetPage();
    }


    public function delete(int $id)
    {
        Gate::authorize('manage-employees');

        $employee = Employee::withCount('payments')->findOrFail($id);
        
        if ($employee->payments_count > 0) {
            return;
        }

        $employee->delete();
        $this->notify(__('common.deleted'));
    }

    public function render()
    {
        $query = Employee::withSum(['advances' => function ($q) {
            $q->whereIn('status', ['pending', 'approved']);
        }], 'amount');

        if ($this->searchName) {
            $query->where('name', 'like', '%' . $this->searchName . '%');
        }

        if ($this->searchStatus) {
            $query->where('status', $this->searchStatus);
        }

        $employees = $query->latest()->paginate(10);

        return view('livewire.employee-list', [
            'employees' => $employees,
        ])->layout('layouts.app')->title(__('nav.employees') ?? 'Employees');
    }
}
