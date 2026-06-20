<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, __('auth.unauthorized'));
        }
        if (!$request->user()->role) {
            abort(403, __('auth.no_role'));
        }
        if (!in_array($request->user()->role->name, $roles)) {
            abort(403, __('auth.unauthorized'));
        }

        return $next($request);
    }
}
