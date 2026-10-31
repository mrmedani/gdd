<?php

use App\Domains\Treasury\Livewire\StationChargesIndex;
use App\Domains\Treasury\Livewire\TreasuryIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:treasury'])->prefix('treasury')->name('treasury.')->group(function () {
    Route::get('/', TreasuryIndex::class)->name('index');
});

// Commissions des stations (agences wilayas) : permission DÉDIÉE 'station-charges',
// indeplissable de treasury (un caissier peut clôturer sans gérer le registre).
Route::middleware(['auth', 'permission:station-charges'])->prefix('treasury')->name('treasury.')->group(function () {
    Route::get('/station-charges', StationChargesIndex::class)->name('station-charges');
});
