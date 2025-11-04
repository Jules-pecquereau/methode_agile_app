<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: Temporaire pour les tests - à réactiver quand l'auth sera en place
        // if (!auth()->check() || auth()->user()->role !== 'manager') {
        //     abort(403, 'Accès réservé aux managers.');
        // }

        return $next($request);
    }
}
