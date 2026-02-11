<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- En-tête avec onglets -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Tableau de bord Administrateur</h1>
            <div class="nav-tabs">
                <a href="#" class="nav-tab active">Vue d'ensemble</a>
                <a href="#" class="nav-tab">Utilisateurs</a>
                <a href="#" class="nav-tab">Système</a>
            </div>
            <div class="text-sm text-gray-500 mt-2">{{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Cartes de statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            <!-- Utilisateurs -->
            <div class="dashboard-card border-primary animate-fadeInUp" style="animation-delay: 0.1s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Utilisateurs</p>
                            <p class="dashboard-stat counter">{{ $stats['utilisateurs'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Magasins -->
            <div class="dashboard-card border-warning animate-fadeInUp" style="animation-delay: 0.2s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Magasins</p>
                            <p class="dashboard-stat counter">{{ $stats['magasins'] ?? 0 }}</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-store text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutiques -->
            <div class="dashboard-card border-success animate-fadeInUp" style="animation-delay: 0.3s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Boutiques</p>
                            <p class="dashboard-stat counter">{{ $stats['boutiques'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-store-alt text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits -->
            <div class="dashboard-card border-info animate-fadeInUp" style="animation-delay: 0.4s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Produits</p>
                            <p class="dashboard-stat counter">{{ $stats['produits'] ?? 0 }}</p>
                        </div>
                        <div class="bg-cyan-100 p-3 rounded-full">
                            <i class="fas fa-box text-cyan-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventes du jour -->
            <div class="dashboard-card border-danger animate-fadeInUp" style="animation-delay: 0.5s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Ventes aujourd'hui</p>
                            <p class="dashboard-stat counter">{{ $stats['ventes_aujourd_hui'] ?? 0 }}</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-shopping-cart text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section principale -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Répartition des utilisateurs -->
            <div class="dashboard-card border-primary animate-fadeInUp" style="animation-delay: 0.6s">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition des utilisateurs</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Administrateurs</span>
                            <span class="badge-admin">{{ $stats['utilisateurs_par_role']['admin'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Gestionnaires</span>
                            <span class="badge-gestionnaire">{{ $stats['utilisateurs_par_role']['gestionnaire'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Vendeurs</span>
                            <span class="badge-vendeur">{{ $stats['utilisateurs_par_role']['vendeur'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="dashboard-card border-success animate-fadeInUp lg:col-span-2" style="animation-delay: 0.7s">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="{{ route('users.index') }}" class="action-card">
                            <div class="bg-blue-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-users-cog text-blue-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Utilisateurs</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les comptes</p>
                            <span class="text-blue-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="{{ route('magasins.index') }}" class="action-card">
                            <div class="bg-yellow-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-building text-yellow-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Magasins</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les magasins</p>
                            <span class="text-yellow-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="{{ route('boutiques.index') }}" class="action-card">
                            <div class="bg-green-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-store-alt text-green-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Boutiques</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les boutiques</p>
                            <span class="text-green-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="{{ route('rapports.index') }}" class="action-card">
                            <div class="bg-purple-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-cogs text-purple-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Configuration</h4>
                            <p class="text-sm text-gray-600 mb-4">Paramètres système</p>
                            <span class="text-purple-600 text-sm font-medium">Configurer →</span>
                        </a>

                        <a href="{{ route('rapports.index') }}" class="action-card">
                            <div class="bg-indigo-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-shield-alt text-indigo-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Sécurité</h4>
                            <p class="text-sm text-gray-600 mb-4">Logs et surveillance</p>
                            <span class="text-indigo-600 text-sm font-medium">Sécuriser →</span>
                        </a>

                        <a href="{{ route('rapports.index') }}" class="action-card">
                            <div class="bg-red-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-database text-red-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Sauvegardes</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les sauvegardes</p>
                            <span class="text-red-600 text-sm font-medium">Sauvegarder →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="dashboard-card border-warning animate-fadeInUp" style="animation-delay: 0.8s">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activité récente</h3>
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Aucune activité récente à afficher</p>
                    <a href="{{ route('rapports.index') }}" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-eye mr-2"></i>Voir toutes les activités
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertes de stock -->
        @if($stockAlerts->count() > 0)
        <div class="dashboard-card border-danger animate-fadeInUp pulse-alert" style="animation-delay: 0.9s">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-red-600 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Alertes de Stock Faible ({{ $stockAlerts->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach($stockAlerts as $alert)
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $alert->produit->nom }}</p>
                            <p class="text-sm text-gray-600">
                                @if($alert instanceof \App\Models\StockMagasin)
                                    Magasin: {{ $alert->magasin->nom }}
                                @else
                                    Boutique: {{ $alert->boutique->nom }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="badge bg-danger">{{ $alert->quantite }} restant(s)</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-file-pdf mr-2"></i>Voir rapport stock complet
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
