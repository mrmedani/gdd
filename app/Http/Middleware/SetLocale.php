<?php

namespace App\Http\Middleware;

use App\Domains\Settings\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->user()?->locale
            ?? Setting::get('default_locale', 'ar');

        if (in_array($locale, ['ar', 'fr', 'en'])) {
            App::setLocale($locale);
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
