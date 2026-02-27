<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        if (auth()->user()->role !== 'patient') {
            return redirect('/')->with('error', 'Access denied. Patients only.');
        }
        return $next($request);
    }
}
