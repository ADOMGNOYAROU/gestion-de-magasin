<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class LimitesHelper
{
    /**
     * Obtenir les limites de stock
     */
    public static function getStockLimits()
    {
        return config('limits.stock');
    }

    /**
     * Obtenir les limites de transfert
     */
    public static function getTransfertLimits()
    {
        return config('limits.transfert');
    }

    /**
     * Obtenir les limites financières
     */
    public static function getFinancierLimits()
    {
        return config('limits.financier');
    }

    /**
     * Obtenir les limites des prix
     */
    public static function getPrixLimits()
    {
        return config('limits.prix');
    }

    /**
     * Obtenir les limites des utilisateurs
     */
    public static function getUtilisateursLimits()
    {
        return config('limits.utilisateurs');
    }

    /**
     * Obtenir les limites générales
     */
    public static function getGeneralLimits()
    {
        return config('limits.general');
    }

    /**
     * Vérifier si une quantité de stock est valide
     */
    public static function isStockQuantiteValide($quantite)
    {
        $limits = self::getStockLimits();
        return $quantite >= $limits['quantite_min'] && $quantite <= $limits['quantite_max'];
    }

    /**
     * Vérifier si une quantité de transfert est valide
     */
    public static function isTransfertQuantiteValide($quantite)
    {
        $limits = self::getTransfertLimits();
        return $quantite >= $limits['quantite_min'] && $quantite <= $limits['quantite_max'];
    }

    /**
     * Vérifier si un montant de transfert est valide
     */
    public static function isTransfertMontantValide($montant)
    {
        $limits = self::getTransfertLimits();
        return $montant >= $limits['montant_min'] && $montant <= $limits['montant_max'];
    }

    /**
     * Vérifier si un montant financier est valide
     */
    public static function isMontantValide($montant)
    {
        $limits = self::getFinancierLimits();
        return $montant >= $limits['transaction_min'] && $montant <= $limits['transaction_max'];
    }

    /**
     * Vérifier si un prix est valide
     */
    public static function isPrixValide($prix)
    {
        $limits = self::getPrixLimits();
        return $prix >= $limits['achat_min'] && $prix <= $limits['vente_max'];
    }

    /**
     * Obtenir le message d'erreur pour une limite dépassée
     */
    public static function getMessageErreur($type, $valeur, $min, $max)
    {
        return "La $type doit être entre $min et $max. Valeur fournie : $valeur";
    }
}
