<?php

namespace App\Services;

use App\Models\Boutique;
use App\Models\Client;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\EntreeStock;
use App\Models\Fournisseur;
use App\Models\Magasin;
use App\Models\Order;
use App\Models\Partenaire;
use App\Models\Produit;
use App\Models\Transfert;
use App\Models\User;
use App\Models\Vente;
use App\Notifications\ActivityNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ActivityNotificationService
{
    /**
     * Modèles dont les écritures (création/modification/suppression) doivent
     * notifier la hiérarchie. Les tables internes/dérivées (lignes de panier,
     * quantités de stock déjà couvertes par l'alerte stock faible, sessions
     * de caisse) sont volontairement exclues pour éviter le bruit.
     *
     * @var array<class-string<Model>, array{0: ?string, 1: ?string, 2: string}>
     */
    private array $modelesSuivis = [
        Produit::class => ['produits', 'nom', 'Produit'],
        Fournisseur::class => ['fournisseurs', 'nom', 'Fournisseur'],
        Partenaire::class => ['partenaires', 'nom', 'Partenaire'],
        Client::class => ['clients', 'nom', 'Client'],
        Boutique::class => ['boutiques', 'nom', 'Boutique'],
        Magasin::class => ['magasins', 'nom', 'Magasin'],
        Order::class => ['orders', 'numero_commande', 'Commande'],
        Vente::class => ['ventes', 'numero_ticket', 'Vente'],
        Transfert::class => ['transferts', null, 'Transfert'],
        EntreeStock::class => ['entrees-stock', null, 'Entrée de stock'],
        Credit::class => ['credits', null, 'Crédit'],
        CreditPayment::class => [null, null, 'Paiement de crédit'],
    ];

    public function estSuivi(Model $model): bool
    {
        return array_key_exists($model::class, $this->modelesSuivis);
    }

    /**
     * Notifie la hiérarchie de l'utilisateur connecté suite à une écriture
     * sur un modèle suivi. Un admin n'a pas de supérieur, donc ses propres
     * actions ne déclenchent rien.
     */
    public function notifier(Model $model, string $verbe): void
    {
        if (!$this->estSuivi($model)) {
            return;
        }

        $acteur = auth()->user();
        if (!$acteur || $acteur->isAdmin()) {
            return;
        }

        $destinataires = User::where('role', 'admin')->get();

        if ($acteur->isVendeur()) {
            $gestionnaire = $acteur->boutique?->magasin?->responsable;
            if ($gestionnaire) {
                $destinataires->push($gestionnaire);
            }
        }

        $destinataires = $destinataires->unique('id');
        if ($destinataires->isEmpty()) {
            return;
        }

        [$routePrefix, $labelAttribut, $label] = $this->modelesSuivis[$model::class];

        $display = $labelAttribut && $model->{$labelAttribut}
            ? (string) $model->{$labelAttribut}
            : '#' . $model->getKey();

        $url = null;
        if ($routePrefix && $model->getKey()) {
            $routeName = "{$routePrefix}.show";
            if (\Illuminate\Support\Facades\Route::has($routeName)) {
                $url = route($routeName, $model->getKey());
            }
        }

        Notification::send(
            $destinataires,
            new ActivityNotification($acteur->name, $verbe, $label, $display, $url)
        );
    }

    public function verbeDepuisEvenement(string $eventName): string
    {
        return match (true) {
            Str::contains($eventName, 'created') => 'créé',
            Str::contains($eventName, 'updated') => 'modifié',
            Str::contains($eventName, 'deleted') => 'supprimé',
            default => 'modifié',
        };
    }
}
