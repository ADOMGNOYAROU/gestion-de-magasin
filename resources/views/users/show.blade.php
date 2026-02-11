@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de l'Utilisateur</h1>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-4">Informations personnelles</h5>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nom complet:</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Rôle:</label>
                                <p class="form-control-plaintext">
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Administrateur</span>
                                    @elseif($user->role == 'gestionnaire')
                                        <span class="badge bg-warning">Gestionnaire</span>
                                    @else
                                        <span class="badge bg-info">Vendeur</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Magasin associé:</label>
                                <p class="form-control-plaintext">
                                    {{ $user->magasin ? $user->magasin->nom : 'Aucun magasin' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="card-title mb-4">Informations système</h5>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ID:</label>
                                <p class="form-control-plaintext">{{ $user->id }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de création:</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Dernière modification:</label>
                                <p class="form-control-plaintext">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Statut:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success">Actif</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($user->id != auth()->id())
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="text-danger mb-3">Actions dangereuses</h5>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer l'utilisateur
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
