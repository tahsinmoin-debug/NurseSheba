<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NurseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        if (auth()->user()->role !== 'nurse') {
            return redirect('/')->with('error', 'Access denied. Nurses only.');
        }
        return $next($request);
    }
}
