@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ __('messages.details_boutique') }}</h1>
                <div>
                    <a href="{{ route('boutiques.edit', $boutique) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('messages.modifier') }}
                    </a>
                    <a href="{{ route('boutiques.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.retour_liste') }}
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.informations_generales') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.nom') }} :</strong> {{ $boutique->nom }}</p>
                            <p><strong>{{ __('messages.adresse') }} :</strong> {{ $boutique->adresse ?? __('messages.non_specifie') }}</p>
                            <p><strong>{{ __('messages.telephone') }} :</strong> {{ $boutique->telephone ?? __('messages.non_specifie') }}</p>
                            <p><strong>{{ __('messages.email') }} :</strong> {{ $boutique->email ?? __('messages.non_specifie') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.magasin') }} :</strong> {{ $boutique->magasin->nom ?? __('messages.non_specifie') }}</p>
                            <p><strong>{{ __('messages.responsable') }} :</strong> {{ $boutique->responsable->name ?? __('messages.non_specifie') }}</p>
                            <p><strong>{{ __('messages.date_creation') }} :</strong> {{ $boutique->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>{{ __('messages.derniere_mise_a_jour') }} :</strong> {{ $boutique->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.stock_boutique') }}</h6>
                </div>
                <div class="card-body">
                    @if($boutique->stockBoutiques->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.produit') }}</th>
                                        <th>{{ __('messages.quantite_stock') }}</th>
                                        <th>{{ __('messages.seuil_alerte') }}</th>
                                        <th>{{ __('messages.statut') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boutique->stockBoutiques as $stock)
                                        <tr>
                                            <td>{{ $stock->produit->nom ?? __('messages.produit_inconnu') }}</td>
                                            <td>{{ $stock->quantite }}</td>
                                            <td>{{ $stock->seuil_alerte ?? __('messages.non_defini') }}</td>
                                            <td>
                                                @if(isset($stock->seuil_alerte) && $stock->quantite <= $stock->seuil_alerte)
                                                    <span class="badge bg-danger">{{ __('messages.stock_faible') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('messages.en_stock') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('messages.aucun_produit_stock') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
