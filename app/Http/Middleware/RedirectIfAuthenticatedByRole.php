<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticatedByRole
{
    public function handle($request, Closure $next)
{
    if (auth()->check()) {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager-area.dashboard'),
            default => redirect('/'),
        };
    }

    return $next($request);
}


}
