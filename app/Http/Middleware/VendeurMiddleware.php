<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class VendeurMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            Log::warning('Tentative d\'accès non authentifiée à une route utilisateur', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            abort(401, 'Authentification requise');
        }

        // Vérifier si l'utilisateur a un rôle valide
        if (!auth()->user()->isAdmin() && !auth()->user()->isGestionnaire() && !auth()->user()->isVendeur()) {
            Log::warning('Tentative d\'accès avec un rôle invalide', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            abort(403, 'Rôle utilisateur non valide');
        }

        return $next($request);
    }
}
