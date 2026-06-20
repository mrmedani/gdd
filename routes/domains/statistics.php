<?php

use App\Domains\Statistics\Livewire\StatisticsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:statistics'])->prefix('statistics')->name('statistics.')->group(function () {
    Route::get('/', StatisticsIndex::class)->name('index');
});
