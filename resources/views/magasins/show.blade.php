@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Magasin</h1>
                <div>
                    <a href="{{ route('magasins.edit', $magasin) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('magasins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Informations du magasin</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom:</label>
                                        <p class="form-control-plaintext">{{ $magasin->nom }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Localisation:</label>
                                        <p class="form-control-plaintext">{{ $magasin->localisation }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Responsable:</label>
                                <p class="form-control-plaintext">
                                    {{ $magasin->responsable ? $magasin->responsable->name . ' (' . $magasin->responsable->email . ')' : 'N/A' }}
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date de création:</label>
                                        <p class="form-control-plaintext">{{ $magasin->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Dernière modification:</label>
                                        <p class="form-control-plaintext">{{ $magasin->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Boutiques associées ({{ $magasin->boutiques->count() }})</h5>

                            @if($magasin->boutiques->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Adresse</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($magasin->boutiques as $boutique)
                                            <tr>
                                                <td>{{ $boutique->nom }}</td>
                                                <td>{{ $boutique->adresse }}</td>
                                                <td>
                                                    <a href="{{ route('boutiques.show', $boutique) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Aucune boutique associée à ce magasin.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Stock du magasin</h5>

                            @if($magasin->stockMagasins->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($magasin->stockMagasins as $stock)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $stock->produit->nom }}
                                        <span class="badge bg-primary rounded-pill">{{ $stock->quantite }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucun stock enregistré.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="text-danger mb-3">Actions dangereuses</h5>
                            @if($magasin->boutiques->count() == 0)
                                <form method="POST" action="{{ route('magasins.destroy', $magasin) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce magasin ? Cette action est irréversible.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Supprimer le magasin
                                    </button>
                                </form>
                            @else
                                <p class="text-muted small">Impossible de supprimer ce magasin car il contient des boutiques.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
