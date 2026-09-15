<?php

use App\Domains\Dashboard\Livewire\Dashboard;
use App\Domains\Dashboard\Livewire\DashboardCommercial;
use Illuminate\Support\Facades\Route;

// Dashboard : les rôles SANS aucune permission financière (ex Commercial) reçoivent
// automatiquement le dashboard spécialisé (contrats + alertes), les autres le normal.
// Avec Livewire full-page components on délègue au composant : la closure serait hors
// contexte Livewire ($__livewire). Route::get('/', PageController-like) n'est adapté que
// pour des pages non-Livewire. On délègue donc à Dashboard::class en lui passant le mode.
Route::middleware('auth')->get('/', Dashboard::class)->name('dashboard');
