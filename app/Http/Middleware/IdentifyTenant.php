<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // Option 1: Identifier le tenant depuis l'utilisateur connecté
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = Auth::user()->tenant;
        }

        // Option 2: Identifier le tenant depuis le sous-domaine (pour future implémentation)
        // Ex: entreprise.tonapp.com
        if (!$tenant && $request->getHost() !== config('app.url')) {
            $subdomain = explode('.', $request->getHost())[0];
            $tenant = Tenant::where('slug', $subdomain)->first();
        }

        // Option 3: Identifier le tenant depuis la session (pour les pages publiques)
        if (!$tenant && session('tenant_id')) {
            $tenant = Tenant::find(session('tenant_id'));
        }

        // Si aucun tenant trouvé et utilisateur connecté sans tenant
        if (!$tenant && Auth::check() && !Auth::user()->tenant_id) {
            // Rediriger vers la page de création de tenant
            if ($request->route()->getName() !== 'tenant.create' && 
                $request->route()->getName() !== 'tenant.store') {
                return redirect()->route('tenant.create')
                    ->with('info', 'Veuillez créer votre entreprise pour continuer.');
            }
        }

        // Si tenant trouvé mais inactif
        if ($tenant && !$tenant->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Contactez le support.');
        }

        // Stocker le tenant dans l'application pour toute la requête
        if ($tenant) {
            app()->instance('currentTenant', $tenant);
            
            // Ajouter le tenant_id à toutes les requêtes de création/mise à jour
            $request->merge(['tenant_id' => $tenant->id]);
        }

        return $next($request);
    }
}
