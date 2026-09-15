<?php

use App\Domains\Contracts\Livewire\ContractsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:contracts'])->prefix('contracts')->name('contracts.')->group(function () {
    Route::get('/', ContractsIndex::class)->name('index');
});
