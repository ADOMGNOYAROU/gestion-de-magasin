<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- En-tête avec onglets -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Tableau de bord</h1>
            <div class="nav-tabs">
                <a href="#" class="nav-tab active">Rapports</a>
                <a href="#" class="nav-tab">Statistiques</a>
                <a href="#" class="nav-tab">Activité</a>
            </div>
            <div class="text-sm text-gray-500 mt-2">{{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <!-- Produits Actifs -->
            <div class="dashboard-card border-primary animate-fadeInUp" style="animation-delay: 0.1s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Produits Actifs</p>
                            <p class="dashboard-stat text-gray-900">{{ $stats['produits_actifs'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Magasin -->
            <div class="dashboard-card border-success animate-fadeInUp" style="animation-delay: 0.2s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Stock Magasin</p>
                            <p class="dashboard-stat text-gray-900">{{ $stats['stock_magasin'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Boutiques -->
            <div class="dashboard-card border-warning animate-fadeInUp" style="animation-delay: 0.3s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Stock Boutiques</p>
                            <p class="dashboard-stat text-gray-900">{{ $stats['stock_boutiques'] ?? 0 }}</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventes du jour -->
            <div class="dashboard-card border-primary animate-fadeInUp" style="animation-delay: 0.4s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Ventes du jour</p>
                            <p class="dashboard-stat text-gray-900">{{ $stats['ventes_du_jour'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième rangée de cartes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- CA du jour -->
            <div class="dashboard-card border-success animate-fadeInUp" style="animation-delay: 0.5s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">CA du jour</p>
                            <p class="dashboard-stat text-gray-900">{{ number_format($stats['ca_jour'] ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CA du mois -->
            <div class="dashboard-card border-warning animate-fadeInUp" style="animation-delay: 0.6s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">CA du mois</p>
                            <p class="dashboard-stat text-gray-900">{{ number_format($stats['ca_mois'] ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bénéfice du mois -->
            <div class="dashboard-card border-danger animate-fadeInUp" style="animation-delay: 0.7s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Bénéfice du mois</p>
                            <p class="dashboard-stat text-gray-900">{{ number_format($stats['benefice_mois'] ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des notifications -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Notifications récentes</h2>
                    <div class="relative">
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Stock faible pour Produit A</p>
                            <p class="text-sm text-gray-500">Il reste moins de 10 unités en stock</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 2 heures</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Nouvelle vente enregistrée</p>
                            <p class="text-sm text-gray-500">Vente #4567 pour 12 500 FCFA</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 5 heures</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Rapport mensuel disponible</p>
                            <p class="text-sm text-gray-500">Le rapport de janvier 2026 est prêt</p>
                            <p class="text-xs text-gray-400 mt-1">Hier</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 text-right">
                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Voir toutes les notifications</a>
            </div>
        </div>
    </div>

    <!-- Menu utilisateur -->
    <div class="fixed top-0 right-0 m-4">
        <div class="relative inline-block text-left">
            <div>
                <button type="button" class="flex items-center text-sm rounded-full focus:outline-none" id="user-menu-button">
                    <span class="sr-only">Ouvrir le menu utilisateur</span>
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-700">Gestionnaire</span>
                    <svg class="ml-1 h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Menu déroulant -->
            <div class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                <div class="py-1" role="none">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Mon Profil</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-1">Paramètres</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour le menu déroulant -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });

            // Fermer le menu si on clique ailleurs
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
