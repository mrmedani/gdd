<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProfileSettings;

Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfileSettings::class)->name('profile');
});
