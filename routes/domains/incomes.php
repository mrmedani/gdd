<?php

use App\Domains\Treasury\Livewire\IncomesIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:incomes'])->prefix('incomes')->name('incomes.')->group(function () {
    Route::get('/', IncomesIndex::class)->name('index');
});
