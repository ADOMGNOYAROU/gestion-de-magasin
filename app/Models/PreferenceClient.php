<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferenceClient extends Model
{
    protected $table = 'preferences_clients';

    protected $fillable = [
        'client_id',
        'categorie_preferee',
        'produit_prefere_id',
        'frequence_achat',
        'budget_moyen',
        'canal_prefere',
        'notifications_email',
        'notifications_sms',
        'tags'
    ];

    protected $casts = [
        'budget_moyen' => 'decimal:2',
        'notifications_email' => 'boolean',
        'notifications_sms' => 'boolean',
        'tags' => 'array'
    ];

    // Relations
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function produitPrefere(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_prefere_id');
    }

    // Méthodes utilitaires
    public function analyserAchatsClient(): void
    {
        $client = $this->client;

        // Analyser la catégorie la plus achetée
        $categoriePreferee = $client->ventes()
            ->join('vente_produits', 'ventes.id', '=', 'vente_produits.vente_id')
            ->join('produits', 'vente_produits.produit_id', '=', 'produits.id')
            ->select('produits.categorie', \DB::raw('COUNT(*) as total'))
            ->groupBy('produits.categorie')
            ->orderBy('total', 'desc')
            ->first();

        // Analyser le produit le plus acheté
        $produitPrefere = $client->ventes()
            ->join('vente_produits', 'ventes.id', '=', 'vente_produits.vente_id')
            ->select('vente_produits.produit_id', \DB::raw('SUM(vente_produits.quantite) as total'))
            ->groupBy('vente_produits.produit_id')
            ->orderBy('total', 'desc')
            ->first();

        // Calculer le budget moyen
        $budgetMoyen = $client->ventes()
            ->select(\DB::raw('AVG(montant_total) as budget_moyen'))
            ->first();

        // Analyser la fréquence d'achat
        $frequence = $this->calculerFrequenceAchat($client);

        $this->update([
            'categorie_preferee' => $categoriePreferee?->categorie,
            'produit_prefere_id' => $produitPrefere?->produit_id,
            'budget_moyen' => $budgetMoyen?->budget_moyen,
            'frequence_achat' => $frequence
        ]);
    }

    private function calculerFrequenceAchat(Client $client): string
    {
        $ventesCount = $client->ventes()->count();
        $premiereVente = $client->ventes()->min('date_vente');
        $derniereVente = $client->ventes()->max('date_vente');

        if (!$premiereVente || !$derniereVente || $ventesCount < 2) {
            return 'occasionnel';
        }

        $joursEntrePremiereEtDerniere = \Carbon\Carbon::parse($premiereVente)
            ->diffInDays(\Carbon\Carbon::parse($derniereVente));

        if ($joursEntrePremiereEtDerniere == 0) {
            return 'occasionnel';
        }

        $frequenceJours = $joursEntrePremiereEtDerniere / ($ventesCount - 1);

        if ($frequenceJours <= 1) {
            return 'quotidien';
        } elseif ($frequenceJours <= 7) {
            return 'hebdomadaire';
        } elseif ($frequenceJours <= 30) {
            return 'mensuel';
        } else {
            return 'occasionnel';
        }
    }

    // Scopes
    public function scopeAvecNotifications($query)
    {
        return $query->where(function($q) {
            $q->where('notifications_email', true)
              ->orWhere('notifications_sms', true);
        });
    }

    public function scopeParCategorie($query, $categorie)
    {
        return $query->where('categorie_preferee', $categorie);
    }

    public function scopeParCanal($query, $canal)
    {
        return $query->where('canal_prefere', $canal);
    }
}
