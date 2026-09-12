<?php

use App\Domains\Alerts\Livewire\AlertsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:alerts'])->prefix('alerts')->name('alerts.')->group(function () {
    Route::get('/', AlertsIndex::class)->name('index');
});
