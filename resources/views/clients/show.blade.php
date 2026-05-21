@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard_admin') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('clients.index') }}">{{ __('messages.clients') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ $client->nom }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ __('messages.details_client') }}</h1>
                <div>
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> {{ __('messages.modifier') }}
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.retour') }}
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.informations_client') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.nom') }}</label>
                                        <p class="form-control-plaintext">{{ $client->nom }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.prenom') }}</label>
                                        <p class="form-control-plaintext">{{ $client->prenom ?: '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.email') }}</label>
                                        <p class="form-control-plaintext">{{ $client->email ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.telephone') }}</label>
                                        <p class="form-control-plaintext">{{ $client->telephone ?: '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.adresse') }}</label>
                                        <p class="form-control-plaintext">{{ $client->adresse ?: '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.date_creation') }}</label>
                                        <p class="form-control-plaintext">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ __('messages.derniere_modification') }}</label>
                                        <p class="form-control-plaintext">{{ $client->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('messages.actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.modifier') }} {{ __('messages.client') }}
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirmer_suppression_client') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> {{ __('messages.supprimer') }} {{ __('messages.client') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
