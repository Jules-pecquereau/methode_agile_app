<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        // Réactivation de la sécurité
        if (! auth()->check() || auth()->user()->role !== 'manager') {
            abort(403, 'Accès réservé aux managers.');
        }

        return $next($request);
    }
}
