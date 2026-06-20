<?php

use App\Domains\Treasury\Livewire\TreasuryIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:treasury'])->prefix('treasury')->name('treasury.')->group(function () {
    Route::get('/', TreasuryIndex::class)->name('index');
});
