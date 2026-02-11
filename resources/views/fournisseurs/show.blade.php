@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Fournisseur</h1>
                <div>
                    <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nom :</strong> {{ $fournisseur->nom }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Contact :</strong> {{ $fournisseur->contact_personne }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Adresse :</strong> {{ $fournisseur->adresse }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Téléphone :</strong> {{ $fournisseur->telephone }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email :</strong> {{ $fournisseur->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
