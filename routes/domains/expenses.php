<?php

use App\Domains\Expenses\Livewire\ExpenseForm;
use App\Domains\Expenses\Livewire\ExpenseList;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('expenses')->name('expenses.')->group(function () {
    Route::get('/', ExpenseList::class)->name('index');
    Route::get('/create', ExpenseForm::class)->name('create');
    Route::get('/{expense}/edit', ExpenseForm::class)->name('edit');
});
