<?php

// Helpers pour les permissions et gates

if (!function_exists('canManageProduits')) {
    /**
     * Vérifie si l'utilisateur peut gérer les produits
     */
    function canManageProduits() {
        return auth()->check() && auth()->user()->can('manage-produits');
    }
}

if (!function_exists('canManageEntreesStock')) {
    /**
     * Vérifie si l'utilisateur peut gérer les entrées de stock
     */
    function canManageEntreesStock() {
        return auth()->check() && auth()->user()->can('manage-entrees-stock');
    }
}

if (!function_exists('canManageTransferts')) {
    /**
     * Vérifie si l'utilisateur peut gérer les transferts
     */
    function canManageTransferts() {
        return auth()->check() && auth()->user()->can('manage-transferts');
    }
}

if (!function_exists('canManageVentes')) {
    /**
     * Vérifie si l'utilisateur peut gérer les ventes
     */
    function canManageVentes() {
        return auth()->check() && auth()->user()->can('manage-ventes');
    }
}

if (!function_exists('canManageRapports')) {
    /**
     * Vérifie si l'utilisateur peut gérer les rapports
     */
    function canManageRapports() {
        return auth()->check() && auth()->user()->can('manage-rapports');
    }
}

if (!function_exists('canViewRapportsPartenaires')) {
    /**
     * Vérifie si l'utilisateur peut voir les rapports partenaires
     */
    function canViewRapportsPartenaires() {
        return auth()->check() && auth()->user()->can('view-rapports-partenaires');
    }
}

if (!function_exists('canViewGlobalStats')) {
    /**
     * Vérifie si l'utilisateur peut voir les statistiques globales
     */
    function canViewGlobalStats() {
        return auth()->check() && auth()->user()->can('view-global-stats');
    }
}

if (!function_exists('canManageMagasin')) {
    /**
     * Vérifie si l'utilisateur peut gérer un magasin spécifique
     */
    function canManageMagasin($magasin) {
        return auth()->check() && auth()->user()->can('manage-magasin', $magasin);
    }
}

if (!function_exists('canManageBoutique')) {
    /**
     * Vérifie si l'utilisateur peut gérer une boutique spécifique
     */
    function canManageBoutique($boutique) {
        return auth()->check() && auth()->user()->can('manage-boutique', $boutique);
    }
}

if (!function_exists('canTransferFromMagasin')) {
    /**
     * Vérifie si l'utilisateur peut transférer depuis un magasin
     */
    function canTransferFromMagasin($magasin) {
        return auth()->check() && auth()->user()->can('transfer-from-magasin', $magasin);
    }
}

if (!function_exists('canSellInBoutique')) {
    /**
     * Vérifie si l'utilisateur peut vendre dans une boutique
     */
    function canSellInBoutique($boutique) {
        return auth()->check() && auth()->user()->can('sell-in-boutique', $boutique);
    }
}

if (!function_exists('canViewStockMagasin')) {
    /**
     * Vérifie si l'utilisateur peut voir le stock d'un magasin
     */
    function canViewStockMagasin($magasin) {
        return auth()->check() && auth()->user()->can('view-stock-magasin', $magasin);
    }
}

if (!function_exists('canViewStockBoutique')) {
    /**
     * Vérifie si l'utilisateur peut voir le stock d'une boutique
     */
    function canViewStockBoutique($boutique) {
        return auth()->check() && auth()->user()->can('view-stock-boutique', $boutique);
    }
}

if (!function_exists('hideIfCannot')) {
    /**
     * Retourne une classe CSS pour cacher un élément si la permission n'est pas accordée
     */
    function hideIfCannot($permission, $resource = null) {
        if (!auth()->check()) {
            return 'd-none';
        }
        
        if ($resource) {
            return auth()->user()->cannot($permission, $resource) ? 'd-none' : '';
        }
        
        return auth()->user()->cannot($permission) ? 'd-none' : '';
    }
}

if (!function_exists('disabledIfCannot')) {
    /**
     * Retourne l'attribut disabled si la permission n'est pas accordée
     */
    function disabledIfCannot($permission, $resource = null) {
        if (!auth()->check()) {
            return 'disabled';
        }
        
        if ($resource) {
            return auth()->user()->cannot($permission, $resource) ? 'disabled' : '';
        }
        
        return auth()->user()->cannot($permission) ? 'disabled' : '';
    }
}
