<?php

use App\Domains\Employees\Livewire\EmployeeForm;
use App\Domains\Employees\Livewire\EmployeeList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:employees'])->group(function () {
    Route::get('/employees', EmployeeList::class)->name('employees.index');
    Route::get('/employees/create', EmployeeForm::class)->name('employees.create');
    Route::get('/employees/{employee}/edit', EmployeeForm::class)->name('employees.edit');
});
