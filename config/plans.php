<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plans d'abonnement SaaS
    |--------------------------------------------------------------------------
    |
    | Configuration des différents plans d'abonnement avec leurs limites
    | et fonctionnalités. Les prix sont en euros par mois.
    |
    */

    'starter' => [
        'name' => 'Starter',
        'description' => 'Idéal pour les petites entreprises',
        'price' => 5000, // Prix mensuel en FCFA
        'price_yearly' => 50000, // Prix annuel en FCFA
        'stripe_price_id' => env('STRIPE_PRICE_STARTER', 'price_starter_monthly'),
        'stripe_price_id_yearly' => env('STRIPE_PRICE_STARTER_YEARLY', 'price_starter_yearly'),
        'paystack_plan_code' => env('PAYSTACK_PLAN_STARTER', 'PLN_starter_monthly'),
        'flutterwave_plan_id' => env('FLUTTERWAVE_PLAN_STARTER', 'starter_monthly'),
        
        // Limites
        'max_users' => 3,
        'max_magasins' => 1,
        'max_boutiques' => 3,
        'max_products' => 500,
        
        // Fonctionnalités
        'features' => [
            'pos' => 'Point de vente',
            'basic_reports' => 'Rapports basiques',
            'stock_management' => 'Gestion des stocks',
            'clients' => 'Gestion des clients',
        ],
        
        // Description des fonctionnalités
        'feature_list' => [
            '3 utilisateurs',
            '1 magasin',
            '3 boutiques',
            '500 produits',
            'Point de vente (POS)',
            'Rapports basiques',
            'Gestion des stocks',
            'Support email',
        ],
    ],

    'pro' => [
        'name' => 'Pro',
        'description' => 'Pour les entreprises en croissance',
        'price' => 8000, // Prix mensuel en FCFA
        'price_yearly' => 80000, // Prix annuel en FCFA
        'stripe_price_id' => env('STRIPE_PRICE_PRO', 'price_pro_monthly'),
        'stripe_price_id_yearly' => env('STRIPE_PRICE_PRO_YEARLY', 'price_pro_yearly'),
        'paystack_plan_code' => env('PAYSTACK_PLAN_PRO', 'PLN_pro_monthly'),
        'flutterwave_plan_id' => env('FLUTTERWAVE_PLAN_PRO', 'pro_monthly'),
        
        'max_users' => 10,
        'max_magasins' => 3,
        'max_boutiques' => 10,
        'max_products' => 2000,
        
        'features' => [
            'pos' => 'Point de vente',
            'advanced_reports' => 'Rapports avancés',
            'stock_management' => 'Gestion des stocks',
            'clients' => 'Gestion des clients',
            'api' => 'Accès API',
            'multi_location' => 'Multi-localisation',
        ],
        
        'feature_list' => [
            '10 utilisateurs',
            '3 magasins',
            '10 boutiques',
            '2000 produits',
            'Point de vente (POS)',
            'Rapports avancés (PDF/Excel)',
            'Gestion des stocks',
            'API REST',
            'Support prioritaire',
        ],
    ],

    'enterprise' => [
        'name' => 'Enterprise',
        'description' => 'Solution complète pour grandes entreprises',
        'price' => 12000, // Prix mensuel en FCFA
        'price_yearly' => 120000, // Prix annuel en FCFA
        'stripe_price_id' => env('STRIPE_PRICE_ENTERPRISE', 'price_enterprise_monthly'),
        'stripe_price_id_yearly' => env('STRIPE_PRICE_ENTERPRISE_YEARLY', 'price_enterprise_yearly'),
        'paystack_plan_code' => env('PAYSTACK_PLAN_ENTERPRISE', 'PLN_enterprise_monthly'),
        'flutterwave_plan_id' => env('FLUTTERWAVE_PLAN_ENTERPRISE', 'enterprise_monthly'),
        
        'max_users' => -1, // Illimité
        'max_magasins' => -1,
        'max_boutiques' => -1,
        'max_products' => -1,
        
        'features' => [
            'all' => 'Toutes les fonctionnalités',
        ],
        
        'feature_list' => [
            'Utilisateurs illimités',
            'Magasins illimités',
            'Boutiques illimitées',
            'Produits illimités',
            'Toutes les fonctionnalités Pro',
            'Intégrations personnalisées',
            'Formation sur site',
            'Support dédié 24/7',
            'SLA garanti',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration générale
    |--------------------------------------------------------------------------
    */
    
    // Devise
    'currency' => 'XAF', // Franc CFA (XAF) pour l'Afrique
    'currency_symbol' => 'FCFA',
    
    // Durée de l'essai gratuit (en jours)
    'trial_days' => 14,
    
    // Jours avant expiration pour envoyer un avertissement
    'warning_days' => 7,
    
    // Page de redirection si abonnement expiré
    'redirect_expired' => 'subscription.expired',
    
    // Page de redirection si abonnement annulé
    'redirect_cancelled' => 'subscription.cancelled',
];
