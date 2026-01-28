<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user belum login, redirect ke login
        if (!session()->has('is_authenticated') || !session('is_authenticated')) {
            return redirect('/login')->with('info', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}
