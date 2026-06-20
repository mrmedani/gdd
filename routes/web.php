<?php

require __DIR__ . '/domains/auth.php';
require __DIR__ . '/domains/dashboard.php';
require __DIR__ . '/domains/expenses.php';
require __DIR__ . '/domains/reports.php';
require __DIR__ . '/domains/settings.php';
require __DIR__ . '/domains/locale.php';
require __DIR__ . '/domains/employees.php';
require __DIR__ . '/domains/treasury.php';
require __DIR__ . '/domains/statistics.php';
require __DIR__ . '/domains/profile.php';

// Route dynamique du manifest PWA (accessible sans auth pour l'installation)
Route::get('/manifest.json', function () {
    $setting = function ($key, $default) {
        return \App\Domains\Settings\Models\Setting::get($key, $default);
    };

    $icons = [
        ['src' => '/icons/icon-72x72.png', 'sizes' => '72x72', 'type' => 'image/png'],
        ['src' => '/icons/icon-96x96.png', 'sizes' => '96x96', 'type' => 'image/png'],
        ['src' => '/icons/icon-128x128.png', 'sizes' => '128x128', 'type' => 'image/png'],
        ['src' => '/icons/icon-144x144.png', 'sizes' => '144x144', 'type' => 'image/png'],
        ['src' => '/icons/icon-152x152.png', 'sizes' => '152x152', 'type' => 'image/png'],
        ['src' => '/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => '/icons/icon-384x384.png', 'sizes' => '384x384', 'type' => 'image/png'],
        ['src' => '/icons/icon-512x512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
    ];

    $customIcon = $setting('pwa_icon', null);
    if ($customIcon) {
        array_unshift($icons, [
            'src' => '/storage/' . $customIcon,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ]);
    }

    return response()->json([
        'name' => $setting('app_name', config('app.name')),
        'short_name' => $setting('pwa_short_name', 'Chronorex'),
        'description' => $setting('pwa_description', 'Application de gestion des dépenses et trésorerie'),
        'start_url' => '/',
        'display' => $setting('pwa_display', 'standalone'),
        'background_color' => $setting('pwa_bg_color', '#f1f5f9'),
        'theme_color' => $setting('pwa_theme_color', '#2563eb'),
        'orientation' => $setting('pwa_orientation', 'portrait-primary'),
        'lang' => app()->getLocale(),
        'dir' => in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr',
        'icons' => $icons,
    ]);
})->name('manifest.json');

// Route réservée aux admins pour les opérations de maintenance
Route::middleware(['auth'])->group(function () {
    Route::get('/fix-env', function () {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'photo')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('photo')->nullable()->after('email');
                });
            }

            \Illuminate\Support\Facades\Artisan::call('storage:link');

            return "Succès ! La base de données a été mise à jour.";
        } catch (\Exception $e) {
            return "Erreur : " . $e->getMessage();
        }
    });

    // WhatsApp worker proxy (évite CORS / mixed content)
    Route::prefix('wa')->group(function () {
        Route::get('status', function () {
            return \Illuminate\Support\Facades\Http::get('http://127.0.0.1:9090/status')->json();
        });
        Route::get('qr', function () {
            return \Illuminate\Support\Facades\Http::get('http://127.0.0.1:9090/qr')->json();
        });
        Route::post('start', function () {
            return \Illuminate\Support\Facades\Http::post('http://127.0.0.1:9090/start')->json();
        });
        Route::post('disconnect', function () {
            return \Illuminate\Support\Facades\Http::post('http://127.0.0.1:9090/disconnect')->json();
        });
        Route::post('send', function (\Illuminate\Http\Request $req) {
            return \Illuminate\Support\Facades\Http::post('http://127.0.0.1:9090/send', $req->all())->json();
        });
    });
});
