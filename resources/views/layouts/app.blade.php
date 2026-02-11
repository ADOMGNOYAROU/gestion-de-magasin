<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Système de Gestion de Stock et Ventes">
    
    <title>{{ config('app.name', 'Gestion Stock') }} - @yield('title', 'Tableau de Bord')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #2e3440;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
            font-size: 14px;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #2e59d9 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            padding: 1.5rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand i {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            min-width: 30px;
            text-align: center;
        }

        .sidebar-brand-text {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-item {
            margin: 0.25rem 0;
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: white;
        }

        .sidebar-link i {
            font-size: 1rem;
            margin-right: 0.75rem;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-link-text {
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-link-text {
            opacity: 0;
            visibility: hidden;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navigation */
        .topbar {
            background-color: white;
            padding: 1rem 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-dropdown {
            position: relative;
        }

        .user-button {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-button:hover {
            background-color: var(--light-color);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Page Content */
        .page-content {
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 50%, #f1f3f4 100%);
            min-height: calc(100vh - 80px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-left: 6px solid transparent;
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            box-shadow: 0 1rem 3rem 0 rgba(58, 59, 69, 0.25);
            transform: translateY(-2px);
        }

        .card.border-primary {
            border-left-color: var(--primary-color);
        }

        .card.border-warning {
            border-left-color: var(--warning-color);
        }

        .card.border-success {
            border-left-color: var(--success-color);
        }

        .card.border-info {
            border-left-color: var(--info-color);
        }

        .card.border-danger {
            border-left-color: var(--danger-color);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--light-color);
            border-top: none;
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid var(--info-color);
        }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        /* Modals */
        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        /* Footer */
        .footer {
            background-color: white;
            border-top: 1px solid #e3e6f0;
            padding: 1.5rem 2rem;
            text-align: center;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .topbar {
                padding: 1rem;
            }

            .page-content {
                padding: 1rem;
            }

            .user-info-text {
                display: none;
            }
        }

        /* Dashboard Stats */
        .dashboard-card {
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        }

        .dashboard-stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .dashboard-stat {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1;
        }

        /* Animated Counters */
        .counter {
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Action Cards */
        .action-card {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: 0.75rem;
            padding: 1.5rem;
            background: white;
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        /* Pulsing Animation for Alerts */
        .pulse-alert {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        /* Loading */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fas fa-warehouse"></i>
            <span class="sidebar-brand-text">Gestion Stock</span>
        </a>
        
        <nav class="sidebar-menu">
            <!-- Dashboard -->
            <div class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-link-text">Tableau de Bord</span>
                </a>
            </div>

            <!-- Produits -->
            @if(canManageProduits())
            <div class="sidebar-item">
                <a href="{{ route('produits.index') }}" class="sidebar-link {{ request()->is('produits*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span class="sidebar-link-text">Produits</span>
                </a>
            </div>
            @endif

            <!-- Gestion Stock -->
            @if(canManageEntreesStock())
            <div class="sidebar-item">
                <a href="{{ route('entrees-stock.index') }}" class="sidebar-link {{ request()->is('entrees-stock*') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span class="sidebar-link-text">Entrées Stock</span>
                </a>
            </div>
            @endif

            <!-- Transferts -->
            @if(canManageTransferts())
            <div class="sidebar-item">
                <a href="{{ route('transferts.index') }}" class="sidebar-link {{ request()->is('transferts*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span class="sidebar-link-text">Transferts Stock</span>
                </a>
            </div>
            @endif

            <!-- Ventes -->
            @if(canManageVentes())
            <div class="sidebar-item">
                <a href="{{ route('ventes.index') }}" class="sidebar-link {{ request()->is('ventes*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="sidebar-link-text">Ventes</span>
                </a>
            </div>
            @endif

            <!-- Caisse (POS) -->
            @if(auth()->user()->isVendeur())
            <div class="sidebar-item">
                <a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->is('pos*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span class="sidebar-link-text">Caisse</span>
                </a>
            </div>
            @endif

            <!-- Rapports -->
            @if(canManageRapports())
            <div class="sidebar-item">
                <a href="{{ route('rapports.index') }}" class="sidebar-link {{ request()->is('rapports*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span class="sidebar-link-text">Rapports</span>
                </a>
            </div>
            @endif

            <!-- Séparateur -->
            <hr class="text-white-50 mx-3 my-2">

            <!-- Administration -->
            @if(auth()->user()->isAdmin())
            <div class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->is('admin*') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-link-text">Dashboard Admin</span>
                </a>
            </div>
            
            <!-- Gestion des utilisateurs -->
            <div class="sidebar-item">
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span class="sidebar-link-text">Gestion Utilisateurs</span>
                </a>
            </div>
            
            <!-- Gestion des magasins -->
            <div class="sidebar-item">
                <a href="{{ route('magasins.index') }}" class="sidebar-link {{ request()->is('magasins*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span class="sidebar-link-text">Gestion Magasins</span>
                </a>
            </div>
            
            <!-- Gestion des boutiques -->
            <div class="sidebar-item">
                <a href="{{ route('boutiques.index') }}" class="sidebar-link {{ request()->is('boutiques*') ? 'active' : '' }}">
                    <i class="fas fa-store-alt"></i>
                    <span class="sidebar-link-text">Gestion Boutiques</span>
                </a>
            </div>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="ms-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                <i class="fas fa-home"></i> Accueil
                            </a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            <div class="topbar-right">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link text-muted position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">Stock faible pour Produit A</a></li>
                        <li><a class="dropdown-item" href="#">Nouvelle vente enregistrée</a></li>
                        <li><a class="dropdown-item" href="#">Rapport mensuel disponible</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Voir toutes les notifications</a></li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="user-button" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="user-info-text">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->role }}</small>
                        </div>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i> Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Paramètres
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-warning alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreurs :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Header -->
            @hasSection('header')
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    @yield('header')
                </div>
            @endif

            <!-- Main Content -->
            <div class="fade-in">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="text-center">
                <small>
                    &copy; {{ date('Y') }} {{ config('app.name', 'Gestion Stock') }}. 
                    Tous droits réservés | 
                    Version 1.0.0
                </small>
            </div>
        </footer>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Mobile handling
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            }
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });

        // Auto-hide sidebar on mobile when clicking links
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
        });

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json',
                    search: "Rechercher:",
                    lengthMenu: "Afficher _MENU_ éléments",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                    paginate: {
                        first: "Premier",
                        last: "Dernier",
                        next: "Suivant",
                        previous: "Précédent"
                    }
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc']]
            });
        });

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Chargement...';
                }
            });
        });

        // Confirm modals
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Format date
        function formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR');
        }
    </script>

    @stack('scripts')
</body>
</html>
