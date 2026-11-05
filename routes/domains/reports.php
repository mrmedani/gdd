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

    // Rapport personnalisé (plage de dates) + partage WhatsApp
    Route::post('/custom-pdf', [ReportController::class, 'customPdf'])->name('custom.pdf');
    Route::post('/custom-excel', [ReportController::class, 'customExcel'])->name('custom.excel');
    Route::post('/share-whatsapp', [ReportController::class, 'shareWhatsApp'])->name('share.whatsapp');
});
