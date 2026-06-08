<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsumer
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has the 'consumer' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'consumer') {
            return redirect()->route('consumer.login');
        }

        return $next($request);
    }
}
