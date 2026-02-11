<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Magasin;
use App\Models\Boutique;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate pour gérer un magasin (admin ou gestionnaire du magasin)
        Gate::define('manage-magasin', function (User $user, Magasin $magasin) {
            // Admin peut gérer tous les magasins
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut gérer seulement son magasin
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $magasin->id) {
                return true;
            }
            
            return false;
        });

        // Gate pour gérer une boutique (admin, gestionnaire du magasin, ou vendeur de la boutique)
        Gate::define('manage-boutique', function (User $user, Boutique $boutique) {
            // Admin peut gérer toutes les boutiques
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut gérer les boutiques de son magasin
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $boutique->magasin_id) {
                return true;
            }
            
            // Vendeur peut gérer seulement sa boutique
            if ($user->isVendeur() && $user->boutique && $user->boutique->id === $boutique->id) {
                return true;
            }
            
            return false;
        });

        // Gate pour voir les statistiques globales (admin uniquement)
        Gate::define('view-global-stats', function (User $user) {
            return $user->isAdmin();
        });

        // Gate pour gérer les produits (admin ou gestionnaire)
        Gate::define('manage-produits', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire();
        });

        // Gate pour gérer les entrées de stock (admin ou gestionnaire)
        Gate::define('manage-entrees-stock', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire();
        });

        // Gate pour gérer les transferts (admin ou gestionnaire)
        Gate::define('manage-transferts', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire();
        });

        // Gate pour gérer les ventes (tous les rôles)
        Gate::define('manage-ventes', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire() || $user->isVendeur();
        });

        // Gate pour gérer les rapports (admin ou gestionnaire)
        Gate::define('manage-rapports', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire();
        });

        // Gate pour voir les rapports partenaires (admin ou gestionnaire)
        Gate::define('view-rapports-partenaires', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire();
        });

        // Gate pour accéder au dashboard (tous les utilisateurs connectés)
        Gate::define('access-dashboard', function (User $user) {
            return $user->isAdmin() || $user->isGestionnaire() || $user->isVendeur();
        });

        // Gate pour créer des transferts depuis un magasin spécifique
        Gate::define('transfer-from-magasin', function (User $user, Magasin $magasin) {
            // Admin peut transférer depuis n'importe quel magasin
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut transférer depuis son magasin seulement
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $magasin->id) {
                return true;
            }
            
            return false;
        });

        // Gate pour vendre dans une boutique spécifique
        Gate::define('sell-in-boutique', function (User $user, Boutique $boutique) {
            // Admin peut vendre dans n'importe quelle boutique
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut vendre dans les boutiques de son magasin
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $boutique->magasin_id) {
                return true;
            }
            
            // Vendeur peut vendre seulement dans sa boutique
            if ($user->isVendeur() && $user->boutique && $user->boutique->id === $boutique->id) {
                return true;
            }
            
            return false;
        });

        // Gate pour voir le stock d'un magasin
        Gate::define('view-stock-magasin', function (User $user, Magasin $magasin) {
            // Admin peut voir tous les stocks
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut voir le stock de son magasin
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $magasin->id) {
                return true;
            }
            
            return false;
        });

        // Gate pour voir le stock d'une boutique
        Gate::define('view-stock-boutique', function (User $user, Boutique $boutique) {
            // Admin peut voir tous les stocks
            if ($user->isAdmin()) {
                return true;
            }
            
            // Gestionnaire peut voir les stocks des boutiques de son magasin
            if ($user->isGestionnaire() && $user->magasinResponsable && $user->magasinResponsable->id === $boutique->magasin_id) {
                return true;
            }
            
            // Vendeur peut voir le stock de sa boutique
            if ($user->isVendeur() && $user->boutique && $user->boutique->id === $boutique->id) {
                return true;
            }
            
            return false;
        });
    }
}
