@extends('layouts.app')

@section('title', 'Fermer la caisse')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Caisse</a></li>
    <li class="breadcrumb-item active">Fermer la caisse</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Fermer la caisse</h1>
        <p class="text-muted mb-0">Session ouverte le {{ $session->date_ouverture->format('d/m/Y à H:i') }}</p>
    </div>
    <div>
        <a href="{{ route('pos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la caisse
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <!-- Résumé de la session -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Résumé de la session
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <div class="h5 mb-1 text-primary">{{ number_format($session->montant_initial, 0, ',', ' ') }}</div>
                            <div class="text-muted small">Montant initial</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <div class="h5 mb-1 text-success">{{ number_format($session->montant_theorique, 0, ',', ' ') }}</div>
                            <div class="text-muted small">Montant théorique</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <div class="h5 mb-1 text-info">{{ $session->ventes()->count() }}</div>
                            <div class="text-muted small">Ventes effectuées</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <div class="h5 mb-1 {{ $session->ecart >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $session->ecart >= 0 ? '+' : '' }}{{ number_format($session->ecart, 0, ',', ' ') }}
                            </div>
                            <div class="text-muted small">Écart</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de fermeture -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Fermeture de session de caisse
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pos.store_close') }}" method="POST">
                    @csrf

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention:</strong> Une fois la session fermée, vous ne pourrez plus effectuer de ventes avec cette session.
                        Vérifiez que le montant compté correspond bien au montant théorique.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="montant_final" class="form-label fw-bold">
                                    Montant final compté <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control text-end"
                                           id="montant_final" name="montant_final"
                                           placeholder="0" min="0" step="0.01" required
                                           value="{{ old('montant_final', $session->montant_theorique) }}">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant_final')
                                    <div class="text-danger mt-1">
                                        <small>{{ $message }}</small>
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Saisissez le montant exact que vous avez compté en caisse.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Écart calculé</label>
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control text-end bg-light"
                                           id="ecart_calcule" readonly
                                           value="{{ $session->ecart >= 0 ? '+' : '' }}{{ number_format($session->ecart, 0, ',', ' ') }} FCFA">
                                    <span class="input-group-text">
                                        <i class="fas {{ $session->ecart >= 0 ? 'fa-plus text-success' : 'fa-minus text-danger' }}"></i>
                                    </span>
                                </div>
                                <div class="form-text">
                                    Écart entre le montant théorique et le montant compté.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">Notes de clôture (optionnel)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Expliquez les écarts importants, problèmes rencontrés, etc..."
                                  maxlength="500">{{ old('notes') }}</textarea>
                        <div class="form-text">
                            Maximum 500 caractères. Utile pour expliquer les écarts ou noter des événements particuliers.
                        </div>
                        @error('notes')
                            <div class="text-danger mt-1">
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                    </div>

                    <!-- Résumé détaillé -->
                    <div class="card border-info mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Détail des ventes de la session
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($session->ventes()->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>N° Ticket</th>
                                                <th>Date/Heure</th>
                                                <th>Méthode</th>
                                                <th>Montant</th>
                                                <th>Articles</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($session->ventes as $vente)
                                            <tr>
                                                <td><code>{{ $vente->numero_ticket }}</code></td>
                                                <td>{{ $vente->date_vente->format('d/m H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $vente->paymentMethod->name }}</span>
                                                </td>
                                                <td class="fw-bold">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ $vente->total_produits }} article(s)</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-primary">
                                                <th colspan="3">TOTAL SESSION</th>
                                                <th>{{ number_format($session->ventes()->sum('montant_total'), 0, ',', ' ') }} FCFA</th>
                                                <th>{{ $session->ventes()->sum('total_produits') }} articles</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">Aucune vente effectuée durant cette session.</p>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-lock me-2"></i>
                            Fermer définitivement la caisse
                        </button>
                        <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Annuler et continuer les ventes
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="text-muted small">Vendeur</div>
                        <div class="fw-bold">{{ $session->vendeur->name }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Boutique</div>
                        <div class="fw-bold">{{ $session->boutique->nom }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Durée de session</div>
                        <div class="fw-bold">{{ $session->date_ouverture->diffForHumans(now(), true) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conseils de sécurité -->
        <div class="card mt-4 border-success">
            <div class="card-body">
                <h6 class="card-title text-success">
                    <i class="fas fa-shield-alt me-2"></i>
                    Conseils de sécurité
                </h6>
                <ul class="mb-0 small">
                    <li>Comptez toujours le montant final en présence d'un témoin</li>
                    <li>Vérifiez que tous les tickets de caisse correspondent aux montants</li>
                    <li>Notez les écarts importants avec une explication détaillée</li>
                    <li>Conservez une copie des rapports de clôture</li>
                    <li>En cas d'écart important, informez immédiatement votre supérieur</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantFinal = document.getElementById('montant_final');
    const ecartCalcule = document.getElementById('ecart_calcule');

    function updateEcart() {
        const montantFinalValue = parseFloat(montantFinal.value) || 0;
        const montantTheorique = {{ $session->montant_theorique }};
        const ecart = montantFinalValue - montantTheorique;

        const ecartText = (ecart >= 0 ? '+' : '') + ecart.toLocaleString('fr-FR') + ' FCFA';
        ecartCalcule.value = ecartText;

        // Changer la couleur selon l'écart
        ecartCalcule.className = 'form-control text-end bg-light';
        if (ecart > 0) {
            ecartCalcule.classList.add('text-success');
        } else if (ecart < 0) {
            ecartCalcule.classList.add('text-danger');
        }
    }

    montantFinal.addEventListener('input', updateEcart);
    updateEcart(); // Calcul initial
});
</script>
@endpush
