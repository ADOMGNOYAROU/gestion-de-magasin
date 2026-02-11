@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Boutiques</h1>
                <a href="{{ route('boutiques.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Boutique
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Magasin</th>
                                    <th>Vendeur</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boutiques as $boutique)
                                <tr>
                                    <td>{{ $boutique->id }}</td>
                                    <td>{{ $boutique->nom }}</td>
                                    <td>{{ $boutique->adresse }}</td>
                                    <td>{{ $boutique->magasin ? $boutique->magasin->nom : 'N/A' }}</td>
                                    <td>{{ $boutique->responsable ? $boutique->responsable->name : 'N/A' }}</td>
                                    <td>{{ $boutique->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('boutiques.show', $boutique) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('boutiques.edit', $boutique) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('boutiques.destroy', $boutique) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette boutique ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $boutiques->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection