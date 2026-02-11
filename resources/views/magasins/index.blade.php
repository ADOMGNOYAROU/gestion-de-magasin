@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Magasins</h1>
                <a href="{{ route('magasins.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Magasin
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
                                    <th>Localisation</th>
                                    <th>Responsable</th>
                                    <th>Nombre de boutiques</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($magasins as $magasin)
                                <tr>
                                    <td>{{ $magasin->id }}</td>
                                    <td>{{ $magasin->nom }}</td>
                                    <td>{{ $magasin->localisation }}</td>
                                    <td>{{ $magasin->responsable ? $magasin->responsable->name : 'N/A' }}</td>
                                    <td>{{ $magasin->boutiques->count() }}</td>
                                    <td>{{ $magasin->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('magasins.show', $magasin) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('magasins.edit', $magasin) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('magasins.destroy', $magasin) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce magasin ?')">
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

                    {{ $magasins->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
