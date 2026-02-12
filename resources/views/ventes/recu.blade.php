@extends('layouts.app')

@section('title', 'Reçu de Vente - ' . $vente->numero_ticket)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt"></i> Reçu de Vente
                        </h4>
                        <div>
                            <button onclick="window.print()" class="btn btn-primary btn-sm">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                            <a href="{{ route('ventes.show', $vente) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- En-tête du reçu -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ $vente->boutique->nom }}</h5>
                            <p class="mb-1">{{ $vente->boutique->magasin->nom }}</p>
                            <p class="mb-1">{{ $vente->boutique->adresse }}</p>
                            <p class="mb-1">{{ $vente->boutique->telephone }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6>N° Ticket: <strong>{{ $vente->numero_ticket }}</strong></h6>
                            <p class="mb-1">Date: {{ $vente->date_vente->format('d/m/Y H:i') }}</p>
                            <p class="mb-1">Vendeur: {{ $vente->user->name }}</p>
                            <p class="mb-1">Paiement: {{ $vente->paymentMethod->nom }}</p>
                        </div>
                    </div>

                    <!-- Détails des produits -->
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead class="border-bottom">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix Unit.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vente->venteProduits as $produit)
                                <tr>
                                    <td>
                                        <strong>{{ $produit->produit->nom }}</strong>
                                        @if($produit->remise > 0)
                                        <br><small class="text-muted">Remise: {{ number_format($produit->remise, 0) }} FCFA</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $produit->quantite }}</td>
                                    <td class="text-end">{{ number_format($produit->prix_unitaire, 0) }} FCFA</td>
                                    <td class="text-end"><strong>{{ number_format($produit->sous_total, 0) }} FCFA</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">{{ number_format($vente->montant_total, 0) }} FCFA</th>
                                </tr>
                                @if($vente->montant_recu > 0)
                                <tr>
                                    <th colspan="3" class="text-end">Montant reçu:</th>
                                    <th class="text-end">{{ number_format($vente->montant_recu, 0) }} FCFA</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Monnaie:</th>
                                    <th class="text-end">{{ number_format($vente->monnaie, 0) }} FCFA</th>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                    <!-- Message de remerciement -->
                    <div class="text-center mt-4">
                        <p class="mb-1"><strong>Merci pour votre achat !</strong></p>
                        <p class="text-muted small">À bientôt chez {{ $vente->boutique->nom }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header .d-flex {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endsection
