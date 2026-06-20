<?php

use App\Domains\Auth\Livewire\ForgotPassword;
use App\Domains\Auth\Livewire\ResetPassword;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', ForgotPassword::class)->name('password.forgot');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
