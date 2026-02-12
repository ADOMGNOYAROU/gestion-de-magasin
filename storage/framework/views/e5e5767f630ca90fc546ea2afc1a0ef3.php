<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- En-tête avec onglets -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Tableau de bord Administrateur</h1>
            <div class="nav-tabs">
                <a href="#" class="nav-tab active">Vue d'ensemble</a>
                <a href="#" class="nav-tab">Utilisateurs</a>
                <a href="#" class="nav-tab">Système</a>
            </div>
            <div class="text-sm text-gray-500 mt-2"><?php echo e(now()->format('d/m/Y H:i')); ?></div>
        </div>

        <!-- Cartes de statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            <!-- Utilisateurs -->
            <div class="dashboard-card border-primary animate-fadeInUp" style="animation-delay: 0.1s">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="dashboard-stat-label">Utilisateurs</p>
                            <p class="dashboard-stat counter"><?php echo e($stats['utilisateurs'] ?? 0); ?></p>
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
                            <p class="dashboard-stat counter"><?php echo e($stats['magasins'] ?? 0); ?></p>
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
                            <p class="dashboard-stat counter"><?php echo e($stats['boutiques'] ?? 0); ?></p>
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
                            <p class="dashboard-stat counter"><?php echo e($stats['produits'] ?? 0); ?></p>
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
                            <p class="dashboard-stat counter"><?php echo e($stats['ventes_aujourd_hui'] ?? 0); ?></p>
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
                            <span class="badge-admin"><?php echo e($stats['utilisateurs_par_role']['admin'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Gestionnaires</span>
                            <span class="badge-gestionnaire"><?php echo e($stats['utilisateurs_par_role']['gestionnaire'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Vendeurs</span>
                            <span class="badge-vendeur"><?php echo e($stats['utilisateurs_par_role']['vendeur'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="dashboard-card border-success animate-fadeInUp lg:col-span-2" style="animation-delay: 0.7s">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <a href="<?php echo e(route('users.index')); ?>" class="action-card">
                            <div class="bg-blue-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-users-cog text-blue-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Utilisateurs</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les comptes</p>
                            <span class="text-blue-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="<?php echo e(route('magasins.index')); ?>" class="action-card">
                            <div class="bg-yellow-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-building text-yellow-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Magasins</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les magasins</p>
                            <span class="text-yellow-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="<?php echo e(route('boutiques.index')); ?>" class="action-card">
                            <div class="bg-green-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-store-alt text-green-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Boutiques</h4>
                            <p class="text-sm text-gray-600 mb-4">Gérer les boutiques</p>
                            <span class="text-green-600 text-sm font-medium">Gérer →</span>
                        </a>

                        <a href="<?php echo e(route('rapports.index')); ?>" class="action-card">
                            <div class="bg-purple-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-cogs text-purple-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Configuration</h4>
                            <p class="text-sm text-gray-600 mb-4">Paramètres système</p>
                            <span class="text-purple-600 text-sm font-medium">Configurer →</span>
                        </a>

                        <a href="<?php echo e(route('rapports.index')); ?>" class="action-card">
                            <div class="bg-indigo-100 p-3 rounded-lg mb-3 text-center">
                                <i class="fas fa-shield-alt text-indigo-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Sécurité</h4>
                            <p class="text-sm text-gray-600 mb-4">Logs et surveillance</p>
                            <span class="text-indigo-600 text-sm font-medium">Sécuriser →</span>
                        </a>

                        <a href="<?php echo e(route('rapports.index')); ?>" class="action-card">
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
                    <a href="<?php echo e(route('rapports.index')); ?>" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-eye mr-2"></i>Voir toutes les activités
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertes de stock -->
        <?php if($stockAlerts->count() > 0): ?>
        <div class="dashboard-card border-danger animate-fadeInUp pulse-alert" style="animation-delay: 0.9s">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-red-600 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Alertes de Stock Faible (<?php echo e($stockAlerts->count()); ?>)
                </h3>
                <div class="space-y-3">
                    <?php $__currentLoopData = $stockAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo e($alert->produit->nom); ?></p>
                            <p class="text-sm text-gray-600">
                                <?php if($alert instanceof \App\Models\StockMagasin): ?>
                                    Magasin: <?php echo e($alert->magasin->nom); ?>

                                <?php else: ?>
                                    Boutique: <?php echo e($alert->boutique->nom); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="badge bg-danger"><?php echo e($alert->quantite); ?> restant(s)</span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('rapports.stock.pdf')); ?>" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-file-pdf mr-2"></i>Voir rapport stock complet
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>