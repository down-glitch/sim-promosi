<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login dan coba akses login page, redirect ke dashboard
        if (session()->has('is_authenticated') && session('is_authenticated')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
