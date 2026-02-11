@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Partenaire</h1>
                <div>
                    <a href="{{ route('partenaires.edit', $partenaire) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('partenaires.index') }}" class="btn btn-secondary">
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
                            <strong>Nom :</strong> {{ $partenaire->nom }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Type de Partenariat :</strong> {{ $partenaire->type_partenariat }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Adresse :</strong> {{ $partenaire->adresse }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Téléphone :</strong> {{ $partenaire->telephone }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email :</strong> {{ $partenaire->email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
