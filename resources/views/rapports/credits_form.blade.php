@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-credit-card"></i> Rapport de Crédits
                </h1>
                <a href="{{ route('rapports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> Personnaliser le rapport
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rapports.credits.pdf') }}" method="POST" id="pdfForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Statut des crédits</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Tous les crédits</option>
                                        <option value="unpaid">Non payés</option>
                                        <option value="overdue">En retard</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client</label>
                                    <select class="form-select" id="client_id" name="client_id">
                                        <option value="">Tous les clients</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
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
                    <form action="{{ route('rapports.credits.excel') }}" method="POST" id="excelForm">
                        @csrf
                        <input type="hidden" name="status" id="excel_status">
                        <input type="hidden" name="client_id" id="excel_client_id">
                        <input type="hidden" name="date_debut" id="excel_date_debut">
                        <input type="hidden" name="date_fin" id="excel_date_fin">
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
                            <h6 class="text-danger">
                                <i class="fas fa-file-pdf"></i> Version PDF
                            </h6>
                            <ul class="small">
                                <li>Résumé des crédits (nombre, montants, soldes)</li>
                                <li>Détail de chaque crédit</li>
                                <li>Informations client et boutique</li>
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
    // Soumission des formulaires
    document.getElementById('pdfForm').addEventListener('submit', function(e) {
        // Copier les valeurs vers le formulaire Excel
        document.getElementById('excel_status').value = document.getElementById('status').value;
        document.getElementById('excel_client_id').value = document.getElementById('client_id').value;
        document.getElementById('excel_date_debut').value = document.getElementById('date_debut').value;
        document.getElementById('excel_date_fin').value = document.getElementById('date_fin').value;
    });

    document.getElementById('excelForm').addEventListener('submit', function(e) {
        // Copier les valeurs vers le formulaire Excel
        document.getElementById('excel_status').value = document.getElementById('status').value;
        document.getElementById('excel_client_id').value = document.getElementById('client_id').value;
        document.getElementById('excel_date_debut').value = document.getElementById('date_debut').value;
        document.getElementById('excel_date_fin').value = document.getElementById('date_fin').value;
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
