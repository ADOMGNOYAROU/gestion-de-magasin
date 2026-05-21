<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer le tenant courant
        $tenant = app('currentTenant');

        // Si pas de tenant, laisser passer (sera géré par IdentifyTenant)
        if (!$tenant) {
            return $next($request);
        }

        // Vérifier si le tenant a un abonnement actif
        if (!$tenant->hasActiveSubscription()) {
            // Routes autorisées même sans abonnement
            $allowedRoutes = [
                'subscription.expired',
                'subscription.show',
                'subscription.upgrade',
                'subscription.process',
                'pricing',
                'logout',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('subscription.expired')
                    ->with('error', 'Votre abonnement a expiré. Veuillez le renouveler pour continuer.');
            }
        }

        return $next($request);
    }
}
