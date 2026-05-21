<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Limites du Système
    |--------------------------------------------------------------------------
    |
    | Configuration des limites et seuils pour le système de gestion de magasin
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Limites de Stock
    |--------------------------------------------------------------------------
    */
    'stock' => [
        'quantite_max' => 999999,        // Quantité maximale par produit en stock
        'quantite_min' => 0,            // Quantité minimale par produit en stock
        'alerte_seuil' => 10,          // Seuil d'alerte de stock bas
        'boutique_quantite_max' => 999999, // Quantité maximale par produit en boutique
        'boutique_quantite_min' => 0,     // Quantité minimale par produit en boutique
        'boutique_alerte_seuil' => 5,  // Seuil d'alerte pour stock boutique
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites de Transfert
    |--------------------------------------------------------------------------
    */
    'transfert' => [
        'quantite_max' => 10000,        // Quantité maximale par transfert
        'quantite_min' => 1,            // Quantité minimale par transfert
        'montant_max' => 999999999,    // Montant maximal par transfert (en FCFA)
        'montant_min' => 0,            // Montant minimal par transfert (en FCFA)
        'par_jour_max' => 50,          // Nombre maximum de transferts par jour
        'par_mois_max' => 500,         // Nombre maximum de transferts par mois
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites Financières
    |--------------------------------------------------------------------------
    */
    'financier' => [
        'caisse_max' => 999999999999,    // Montant maximal en caisse (en FCFA) - ILLIMITÉ
        'caisse_min' => 0,             // Montant minimal en caisse (en FCFA)
        'transaction_max' => 999999999999, // Montant maximal par transaction (en FCFA) - ILLIMITÉ
        'transaction_min' => 0,         // Montant minimal par transaction (en FCFA)
        'credit_max' => 999999999999,   // Montant maximal de crédit accordé (en FCFA) - ILLIMITÉ
        'credit_min' => 0,             // Montant minimal de crédit accordé (en FCFA)
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites des Prix
    |--------------------------------------------------------------------------
    */
    'prix' => [
        'achat_max' => 999999999,      // Prix d'achat maximal (en FCFA)
        'achat_min' => 0,              // Prix d'achat minimal (en FCFA)
        'vente_max' => 999999999,      // Prix de vente maximal (en FCFA)
        'vente_min' => 0,              // Prix de vente minimal (en FCFA)
        'marge_min' => 0,              // Marge bénéficiaire minimale (en %)
        'marge_max' => 100,            // Marge bénéficiaire maximale (en %)
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites Utilisateurs
    |--------------------------------------------------------------------------
    */
    'utilisateurs' => [
        'par_magasin_max' => 50,        // Nombre maximum d'utilisateurs par magasin
        'par_boutique_max' => 10,      // Nombre maximum d'utilisateurs par boutique
        'credit_client_max' => 10,      // Nombre maximum de crédits par client
        'vente_par_jour_max' => 100,  // Nombre maximum de ventes par jour par utilisateur
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites Générales
    |--------------------------------------------------------------------------
    */
    'general' => [
        'produits_par_page' => 50,      // Nombre de produits affichés par page
        'rapports_par_jour_max' => 20,  // Nombre maximum de rapports générés par jour
        'upload_taille_max' => 10240,   // Taille maximale des fichiers uploadés (en KB)
        'session_timeout' => 480,        // Timeout de session (en minutes)
    ],
];
