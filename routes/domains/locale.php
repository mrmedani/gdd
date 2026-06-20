<?php

use Illuminate\Support\Facades\Route;

Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'fr', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return redirect()->back();
})->name('locale.switch');
