@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ __('messages.product_list') }}</h1>
                <div>
                    <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-outline-danger me-2 {{ hideIfCannot('manage-rapports') }}">
                        <i class="fas fa-warehouse"></i> {{ __('messages.stock_report') }}
                    </a>
                    <a href="{{ route('produits.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filtre de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('produits.index') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="{{ __('messages.search') }} {{ __('messages.product_name') }} {{ __('messages.category') }}..." 
                                       value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                                </button>
                                @if($search ?? null)
                                    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des produits -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.category') }}</th>
                                    <th>{{ __('messages.purchase_price') }}</th>
                                    <th>{{ __('messages.selling_price') }}</th>
                                    <th>{{ __('messages.price') }} %</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produits as $produit)
                                    <tr>
                                        <td>{{ $produit->id }}</td>
                                        <td>
                                            <strong>{{ $produit->nom }}</strong>
                                            @if($produit->description)
                                                <br><small class="text-muted">{{ Str::limit($produit->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $produit->categorie }}</span>
                                        </td>
                                        <td>{{ number_format($produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $produit->marge_percentage > 30 ? 'success' : ($produit->marge_percentage > 15 ? 'warning' : 'danger') }}">
                                                {{ $produit->marge_percentage }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $produit->statut == 'actif' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($produit->statut) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('produits.show', $produit->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('produits.edit', $produit->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('produits.destroy', $produit->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun produit trouvé</p>
                                            <a href="{{ route('produits.create') }}" class="btn btn-primary">
                                                Créer le premier produit
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($produits->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $produits->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
