@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Comparaison des Prix entre Fournisseurs</h1>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Retour aux commandes
                    </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle commande
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Prix par Produit et Fournisseur</h6>
                </div>
                <div class="card-body">
                    @if($produits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        @foreach($suppliers as $supplier)
                                            <th>{{ $supplier->nom }}</th>
                                        @endforeach
                                        <th>Prix le plus bas</th>
                                        <th>Écart max</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produits as $produit)
                                    <tr>
                                        <td>
                                            <strong>{{ $produit->nom }}</strong><br>
                                            <small class="text-muted">{{ $produit->categorie }}</small>
                                        </td>
                                        @php
                                            $prices = [];
                                            $lowestPrice = null;
                                            $lowestSupplier = null;
                                        @endphp
                                        @foreach($suppliers as $supplier)
                                            @php
                                                $lastOrderItem = \DB::table('order_items')
                                                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                                    ->where('orders.fournisseur_id', $supplier->id)
                                                    ->where('order_items.produit_id', $produit->id)
                                                    ->orderBy('orders.date_commande', 'desc')
                                                    ->first();
                                                $price = $lastOrderItem ? $lastOrderItem->prix_unitaire : null;
                                                $prices[] = $price;
                                                if ($price && (!$lowestPrice || $price < $lowestPrice)) {
                                                    $lowestPrice = $price;
                                                    $lowestSupplier = $supplier->nom;
                                                }
                                            @endphp
                                            <td>
                                                @if($price)
                                                    {{ number_format($price, 0) }} FCFA
                                                    <br><small class="text-muted">Dernière commande</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            @if($lowestPrice)
                                                <strong>{{ number_format($lowestPrice, 0) }} FCFA</strong><br>
                                                <small class="text-success">{{ $lowestSupplier }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(count(array_filter($prices)) > 1)
                                                @php
                                                    $validPrices = array_filter($prices);
                                                    $maxPrice = max($validPrices);
                                                    $minPrice = min($validPrices);
                                                    $ecart = $maxPrice - $minPrice;
                                                    $pourcentage = $minPrice > 0 ? round(($ecart / $minPrice) * 100, 1) : 0;
                                                @endphp
                                                {{ number_format($ecart, 0) }} FCFA<br>
                                                <small class="text-warning">{{ $pourcentage }}%</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5>Aucun produit trouvé</h5>
                            <p class="text-muted">Ajoutez des produits pour voir la comparaison des prix.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
