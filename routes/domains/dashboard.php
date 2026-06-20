<?php

use App\Domains\Dashboard\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/', Dashboard::class)->name('dashboard');
