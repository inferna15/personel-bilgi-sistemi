<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChechAdminRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->getRoleNames()->first() !== 'Admin'){
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
