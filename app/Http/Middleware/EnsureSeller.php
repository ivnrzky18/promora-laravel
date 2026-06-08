<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has the 'seller' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'seller') {
            return redirect()->route('seller.login');
        }

        return $next($request);
    }
}
