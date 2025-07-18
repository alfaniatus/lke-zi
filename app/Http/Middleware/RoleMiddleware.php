<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // if (Auth::check() || !$request->user()->hasRole($role)) {
        //     abort(403, 'Unauthorized.');
        // }

        // return $next($request);

        if(Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}
