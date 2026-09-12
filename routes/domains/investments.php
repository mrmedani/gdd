<?php

use App\Domains\Treasury\Livewire\InvestmentsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:investments'])->prefix('investments')->name('investments.')->group(function () {
    Route::get('/', InvestmentsIndex::class)->name('index');
});
