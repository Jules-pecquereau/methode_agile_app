<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérification avec Bouncer : l'utilisateur doit avoir le rôle "manager"
        if (!auth()->check() || !$request->user()->isAn('manager')) {
            abort(403, 'Accès réservé aux managers.');
        }

        return $next($request);
    }
}
