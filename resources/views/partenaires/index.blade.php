@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Partenaires</h1>
                <a href="{{ route('partenaires.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un Partenaire
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
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Partenaires</h6>
                </div>
                <div class="card-body">
                    @if($partenaires->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Adresse</th>
                                        <th>Téléphone</th>
                                        <th>Email</th>
                                        <th>Type de Partenariat</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partenaires as $partenaire)
                                        <tr>
                                            <td>{{ $partenaire->nom }}</td>
                                            <td>{{ $partenaire->adresse ?: '-' }}</td>
                                            <td>{{ $partenaire->telephone ?: '-' }}</td>
                                            <td>{{ $partenaire->email ?: '-' }}</td>
                                            <td>{{ $partenaire->type_partenariat ?: '-' }}</td>
                                            <td>
                                                <a href="{{ route('partenaires.edit', $partenaire) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('partenaires.destroy', $partenaire) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $partenaires->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun partenaire trouvé</h5>
                            <p class="text-muted">Commencez par ajouter votre premier partenaire.</p>
                            <a href="{{ route('partenaires.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un Partenaire
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
