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

// Diagnostic WhatsApp (accessible sans auth)
Route::get('/wa-diagnostic', function () {
    $results = [];
    // 1. curl_exec
    $ch = curl_init('http://127.0.0.1:9090/status');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_FAILONERROR => false,
    ]);
    $r1 = curl_exec($ch);
    $e1 = curl_error($ch);
    $i1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $results['curl_exec_result'] = $r1 ?: 'NULL/EMPTY';
    $results['curl_error'] = $e1 ?: 'none';
    $results['http_code'] = $i1;

    // 2. shell_exec
    $r2 = @shell_exec('curl -s --connect-timeout 3 --max-time 5 -4 http://127.0.0.1:9090/status 2>&1');
    $results['shell_exec'] = $r2 ?: 'NULL/EMPTY';

    // 3. exec
    @exec('curl -s --connect-timeout 3 --max-time 5 -4 http://127.0.0.1:9090/status 2>&1', $out, $code);
    $results['exec'] = implode("\n", $out) ?: 'NULL/EMPTY';
    $results['exec_code'] = $code;

    // 4. WhatsAppService directement
    $results['WhatsAppService::getStatus'] = \App\Services\WhatsAppService::getStatus('http://127.0.0.1:9090');

    return response()->json($results);
});

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
        'display_override' => ['window-controls-overlay', 'standalone', 'minimal-ui'],
        'background_color' => $setting('pwa_bg_color', '#0f172a'),
        'theme_color' => $setting('pwa_theme_color', '#3b82f6'),
        'orientation' => $setting('pwa_orientation', 'portrait-primary'),
        'categories' => ['finance', 'productivity', 'business'],
        'lang' => app()->getLocale(),
        'dir' => in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr',
        'icons' => $icons,
        'shortcuts' => [
            [
                'name' => __('dashboard.dashboard', [], app()->getLocale()),
                'short_name' => 'Dashboard',
                'description' => 'Voir le tableau de bord',
                'url' => '/',
                'icons' => [['src' => '/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png']]
            ],
            [
                'name' => __('expenses.add_expense', [], app()->getLocale()),
                'short_name' => 'Nouvelle',
                'description' => 'Ajouter une nouvelle dépense',
                'url' => '/expenses?action=create',
                'icons' => [['src' => '/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png']]
            ]
        ]
    ]);
})->name('manifest.json');

// Quitter le mode impersonation
Route::post('/leave-impersonation', function () {
    if (!session()->has('impersonator_id')) {
        return redirect()->route('dashboard');
    }

    $impersonatorId = session('impersonator_id');
    session()->forget('impersonator_id');
    session()->forget('impersonator_name');

    Auth::loginUsingId($impersonatorId);
    session()->regenerate();

    return redirect()->route('settings.users');
})->middleware('auth')->name('leave-impersonation');

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
            return \App\Services\WhatsAppService::getStatus('http://127.0.0.1:9090')
                ?? ['status' => 'error'];
        });
        Route::get('qr', function () {
            $qr = \App\Services\WhatsAppService::getQr('http://127.0.0.1:9090');
            return ['qr' => $qr];
        });
        Route::post('start', function () {
            $ok = \App\Services\WhatsAppService::startWorker('http://127.0.0.1:9090');
            return ['status' => $ok ? 'ok' : 'error'];
        });
        Route::post('disconnect', function () {
            $ok = \App\Services\WhatsAppService::disconnect('http://127.0.0.1:9090');
            return ['status' => $ok ? 'ok' : 'error'];
        });
        Route::post('send', function (\Illuminate\Http\Request $req) {
            $ok = \App\Services\WhatsAppService::sendMessage(
                'http://127.0.0.1:9090',
                $req->input('chatId', ''),
                $req->input('message', '')
            );
            return ['ok' => $ok];
        });
    });
});
