@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-chart-line"></i> Rapport de Ventes
                </h1>
                <a href="{{ route('rapports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Personnaliser le rapport
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rapports.ventes.pdf') }}" method="POST" id="pdfForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" required value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(Auth::user()->isAdmin())
                                    <div class="mb-3">
                                        <label for="magasin_id" class="form-label">Magasin</label>
                                        <select class="form-select" id="magasin_id" name="magasin_id">
                                            <option value="">Tous les magasins</option>
                                            @foreach($magasins as $magasin)
                                                <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="boutique_id" class="form-label">Boutique</label>
                                    <select class="form-select" id="boutique_id" name="boutique_id">
                                        <option value="">Toutes les boutiques</option>
                                        @foreach($boutiques as $boutique)
                                            <option value="{{ $boutique->id }}" 
                                                    data-magasin="{{ $boutique->magasin->id ?? '' }}">
                                                {{ $boutique->nom }}
                                                @if(isset($boutique->magasin))
                                                    ({{ $boutique->magasin->nom }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" form="pdfForm" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> Exporter PDF
                                        </button>
                                        <button type="submit" form="excelForm" class="btn btn-success ms-2">
                                            <i class="fas fa-file-excel"></i> Exporter Excel
                                        </button>
                                    </div>
                                    <div class="text-muted">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            Les filtres sont optionnels. Laissez vide pour tout inclure.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Formulaire caché pour Excel -->
                    <form action="{{ route('rapports.ventes.excel') }}" method="POST" id="excelForm">
                        @csrf
                        <input type="hidden" name="date_debut" id="excel_date_debut">
                        <input type="hidden" name="date_fin" id="excel_date_fin">
                        <input type="hidden" name="magasin_id" id="excel_magasin_id">
                        <input type="hidden" name="boutique_id" id="excel_boutique_id">
                    </form>
                </div>
            </div>

            <!-- Informations sur le rapport -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Contenu du rapport
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-file-pdf"></i> Version PDF
                            </h6>
                            <ul class="small">
                                <li>Résumé des ventes (nombre, CA, bénéfice)</li>
                                <li>Ventes groupées par boutique</li>
                                <li>Ventes groupées par produit</li>
                                <li>Détail complet de chaque vente</li>
                                <li>Format imprimable optimisé</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-file-excel"></i> Version Excel
                            </h6>
                            <ul class="small">
                                <li>Mêmes informations que le PDF</li>
                                <li>Format tableur pour analyse</li>
                                <li>Colonnes triables et filtrables</li>
                                <li>Calculs automatiques possibles</li>
                                <li>Mise en forme professionnelle</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Périodes rapides -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock"></i> Périodes rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('today')">
                                Aujourd'hui
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('yesterday')">
                                Hier
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('week')">
                                Cette semaine
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('month')">
                                Ce mois
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('lastmonth')">
                                Mois dernier
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setPeriod('year')">
                                Cette année
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du filtre boutique par magasin
    const magasinSelect = document.getElementById('magasin_id');
    const boutiqueSelect = document.getElementById('boutique_id');
    
    if (magasinSelect) {
        magasinSelect.addEventListener('change', function() {
            const magasinId = this.value;
            
            // Réinitialiser les boutiques
            boutiqueSelect.innerHTML = '<option value="">Toutes les boutiques</option>';
            
            if (magasinId) {
                // Filtrer les boutiques par magasin
                const options = boutiqueSelect.querySelectorAll('option[data-magasin]');
                options.forEach(option => {
                    if (option.dataset.magasin == magasinId) {
                        boutiqueSelect.appendChild(option.cloneNode(true));
                    }
                });
            } else {
                // Réafficher toutes les boutiques
                const options = boutiqueSelect.querySelectorAll('option[data-magasin]');
                options.forEach(option => {
                    boutiqueSelect.appendChild(option.cloneNode(true));
                });
            }
        });
    }

    // Soumission des formulaires
    document.getElementById('pdfForm').addEventListener('submit', function(e) {
        // Copier les valeurs vers le formulaire Excel
        document.getElementById('excel_date_debut').value = document.getElementById('date_debut').value;
        document.getElementById('excel_date_fin').value = document.getElementById('date_fin').value;
        document.getElementById('excel_magasin_id').value = document.getElementById('magasin_id')?.value || '';
        document.getElementById('excel_boutique_id').value = document.getElementById('boutique_id').value;
    });

    document.getElementById('excelForm').addEventListener('submit', function(e) {
        // Copier les valeurs vers le formulaire Excel
        document.getElementById('excel_date_debut').value = document.getElementById('date_debut').value;
        document.getElementById('excel_date_fin').value = document.getElementById('date_fin').value;
        document.getElementById('excel_magasin_id').value = document.getElementById('magasin_id')?.value || '';
        document.getElementById('excel_boutique_id').value = document.getElementById('boutique_id').value;
    });
});

// Fonctions pour les périodes rapides
function setPeriod(period) {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const today = new Date();
    
    switch(period) {
        case 'today':
            dateDebut.value = formatDate(today);
            dateFin.value = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            dateDebut.value = formatDate(yesterday);
            dateFin.value = formatDate(yesterday);
            break;
        case 'week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            dateDebut.value = formatDate(startOfWeek);
            dateFin.value = formatDate(today);
            break;
        case 'month':
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            dateDebut.value = formatDate(startOfMonth);
            dateFin.value = formatDate(today);
            break;
        case 'lastmonth':
            const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            dateDebut.value = formatDate(startOfLastMonth);
            dateFin.value = formatDate(endOfLastMonth);
            break;
        case 'year':
            const startOfYear = new Date(today.getFullYear(), 0, 1);
            dateDebut.value = formatDate(startOfYear);
            dateFin.value = formatDate(today);
            break;
    }
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>
@endsection
