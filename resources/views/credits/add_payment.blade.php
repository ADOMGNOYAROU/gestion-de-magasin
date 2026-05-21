@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    Ajouter un Paiement - {{ $credit->client->nom }} {{ $credit->client->prenom ?: '' }}
                </h1>
                <div>
                    <a href="{{ route('credits.show', $credit) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informations du Crédit</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Client :</strong> {{ $credit->client->nom }} {{ $credit->client->prenom ?: '' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Montant Total :</strong> {{ number_format($credit->total_amount, 0) }} FCFA</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Solde Restant :</strong> {{ number_format($credit->remaining_balance, 0) }} FCFA</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status :</strong>
                                        <span class="badge bg-warning">Actif</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Ajouter un Paiement</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('credits.store_payment', $credit) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Montant du Paiement *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="amount" name="amount"
                                               value="{{ old('amount') }}" step="0.01" min="0.01"
                                               max="{{ $credit->remaining_balance }}" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    <div class="form-text">Maximum : {{ number_format($credit->remaining_balance, 0) }} FCFA</div>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Date du Paiement *</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date"
                                           value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                    @error('payment_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (optionnel)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Ajouter des notes sur ce paiement...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Enregistrer le Paiement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
