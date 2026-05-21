@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-building mr-2"></i>Créer votre entreprise
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <p class="text-gray-600 mb-4">
                        Pour utiliser l'application, vous devez créer une entreprise (tenant). 
                        Choisissez votre plan et remplissez les informations ci-dessous.
                    </p>

                    <form action="{{ route('tenant.store') }}" method="POST">
                        @csrf

                        <!-- Plan Selection -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Choisissez votre plan</label>
                            <div class="row">
                                @foreach(config('plans') as $key => $plan)
                                    @if($key !== 'currency' && $key !== 'currency_symbol' && $key !== 'trial_days' && $key !== 'warning_days' && $key !== 'redirect_expired' && $key !== 'redirect_cancelled')
                                    <div class="col-md-4 mb-3">
                                        <label class="card h-100 border-2 cursor-pointer 
                                               {{ old('plan') === $key ? 'border-primary bg-light' : 'border-secondary' }} 
                                               hover:border-primary transition">
                                            <input type="radio" name="plan" value="{{ $key }}" class="d-none"
                                                   {{ old('plan') === $key || $key === 'starter' ? 'checked' : '' }}>
                                            <div class="card-body text-center p-3">
                                                <h5 class="card-title font-bold">{{ $plan['name'] }}</h5>
                                                <p class="card-text text-3xl font-bold text-primary my-2">
                                                    {{ $plan['price'] }}€<span class="text-sm text-muted">/mois</span>
                                                </p>
                                                <p class="card-text text-muted small">{{ $plan['description'] }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            @if($errors->has('plan'))
                            <p class="text-danger small mt-1">{{ $errors->first('plan') }}</p>
                            @endif
                        </div>

                        <!-- Company Information -->
                        <div class="mb-4">
                            <h5 class="font-weight-bold mb-3">Informations de l'entreprise</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom de l'entreprise *</label>
                                    <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                           class="form-control @error('company_name') is-invalid @enderror"
                                           placeholder="Votre entreprise">
                                    @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                           class="form-control"
                                           placeholder="+33 6 12 34 56 78">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" name="address" value="{{ old('address') }}"
                                           class="form-control"
                                           placeholder="123 Rue Example">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ville</label>
                                    <input type="text" name="city" value="{{ old('city') }}"
                                           class="form-control"
                                           placeholder="Paris">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Code postal</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                           class="form-control"
                                           placeholder="75001">
                                </div>
                            </div>
                        </div>

                        <!-- Trial Info -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Vous bénéficierez d'un essai gratuit de {{ config('plans.trial_days', 14) }} jours. 
                            Aucune carte bancaire n'est requise pour commencer.
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/logout" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-rocket mr-2"></i>Créer mon entreprise
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
