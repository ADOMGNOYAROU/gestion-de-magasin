@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Fournisseurs</h1>
                <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un Fournisseur
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreurs :</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Fournisseurs</h6>
                </div>
                <div class="card-body">
                    @if($fournisseurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Contact</th>
                                        <th>Adresse</th>
                                        <th>Téléphone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fournisseurs as $fournisseur)
                                        <tr>
                                            <td>{{ $fournisseur->nom }}</td>
                                            <td>{{ $fournisseur->contact_personne ?: '-' }}</td>
                                            <td>{{ $fournisseur->adresse ?: '-' }}</td>
                                            <td>{{ $fournisseur->telephone ?: '-' }}</td>
                                            <td>{{ $fournisseur->email ?: '-' }}</td>
                                            <td>
                                                <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $fournisseurs->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun fournisseur trouvé</h5>
                            <p class="text-muted">Commencez par ajouter votre premier fournisseur.</p>
                            <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un Fournisseur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
