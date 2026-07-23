<?php

use App\Domains\Settings\Livewire\AuditLogs;
use App\Domains\Settings\Livewire\Categories;
use App\Domains\Settings\Livewire\DatabaseBackup;
use App\Domains\Settings\Livewire\Roles;
use App\Domains\Settings\Livewire\Settings;
use App\Domains\Settings\Livewire\Users;
use App\Domains\Settings\Livewire\WhatsappMessages;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', Settings::class)->middleware('permission:settings')->name('index');
    Route::get('/users', Users::class)->middleware('permission:users')->name('users');
    Route::get('/roles', Roles::class)->middleware('permission:roles')->name('roles');
    Route::get('/categories', Categories::class)->middleware('permission:categories')->name('categories');
    Route::get('/whatsapp-messages', WhatsappMessages::class)->middleware('permission:settings')->name('whatsapp-messages');
    Route::get('/audit-logs', AuditLogs::class)->middleware('permission:audit-logs')->name('audit-logs');
    Route::get('/database-backup', DatabaseBackup::class)->middleware('permission:settings')->name('backup');
    Route::get('/backup/download/{filename}', function (string $filename) {
        $path = storage_path('app/backups/' . basename($filename));
        abort_unless(file_exists($path), 404);
        return response()->download($path);
    })->middleware('permission:settings')->name('backup.download');
});
