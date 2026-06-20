<?php

use App\Domains\Reports\Controllers\ReportController;
use App\Domains\Reports\Livewire\ReportsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:reports'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', ReportsIndex::class)->name('index');
    Route::post('/monthly-pdf', [ReportController::class, 'monthlyPdf'])->name('monthly.pdf');
    Route::post('/annual-pdf', [ReportController::class, 'annualPdf'])->name('annual.pdf');
    Route::post('/monthly-excel', [ReportController::class, 'monthlyExcel'])->name('monthly.excel');
    Route::post('/annual-excel', [ReportController::class, 'annualExcel'])->name('annual.excel');
});
