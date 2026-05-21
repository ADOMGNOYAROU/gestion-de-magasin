<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return $next($request);
        }

        // Vérifier les limites selon la route
        $routeName = $request->route()->getName();

        // Limite utilisateurs
        if (str_contains($routeName, 'users.') && str_contains($routeName, 'store')) {
            if (!$tenant->canAddUser()) {
                return back()->with('error', 'Limite d\'utilisateurs atteinte pour votre plan. Upgradez pour ajouter plus d\'utilisateurs.');
            }
        }

        // Limite magasins
        if (str_contains($routeName, 'magasins.') && str_contains($routeName, 'store')) {
            if (!$tenant->canAddMagasin()) {
                return back()->with('error', 'Limite de magasins atteinte pour votre plan. Upgradez pour ajouter plus de magasins.');
            }
        }

        // Limite boutiques
        if (str_contains($routeName, 'boutiques.') && str_contains($routeName, 'store')) {
            if (!$tenant->canAddBoutique()) {
                return back()->with('error', 'Limite de boutiques atteinte pour votre plan. Upgradez pour ajouter plus de boutiques.');
            }
        }

        // Limite produits
        if (str_contains($routeName, 'produits.') && str_contains($routeName, 'store')) {
            $plan = $tenant->getPlanConfig();
            $maxProducts = $plan['max_products'] ?? -1;
            
            if ($maxProducts !== -1) {
                $currentProducts = $tenant->produits()->count();
                if ($currentProducts >= $maxProducts) {
                    return back()->with('error', 'Limite de produits atteinte pour votre plan. Upgradez pour ajouter plus de produits.');
                }
            }
        }

        return $next($request);
    }
}
