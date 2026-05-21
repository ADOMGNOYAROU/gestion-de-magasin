<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="Système de Gestion de Stock et Ventes">
    <meta name="theme-color" content="#4e73df">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    
    <title><?php echo e(config('app.name', 'Gestion Stock')); ?> - <?php echo $__env->yieldContent('title', 'Tableau de Bord'); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    
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
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
            z-index: 1001;
            position: relative;
            min-height: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .main-content.expanded {
                margin-left: 0 !important;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* Mobile navigation adjustments */
            .topbar-left {
                flex: 1;
            }

            .breadcrumb {
                font-size: 0.875rem;
                overflow-x: auto;
                white-space: nowrap;
            }

            /* Mobile buttons */
            .btn {
                min-height: 44px;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                touch-action: manipulation;
            }

            .btn-sm {
                min-height: 36px;
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .btn-mobile {
                width: 100%;
                margin-bottom: 1rem;
            }

            /* Mobile POS specific */
            .product-btn-mobile {
                min-height: 80px;
                padding: 0.5rem;
                margin-bottom: 0.5rem;
                font-size: 0.85rem;
                border-radius: 0.5rem;
                transition: all 0.2s ease;
                touch-action: manipulation;
            }

            .product-btn-mobile:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }

            .product-btn-mobile:active:not(:disabled) {
                transform: translateY(0);
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            }

            @media (max-width: 576px) {
                .product-btn-mobile {
                    min-height: 70px;
                    font-size: 0.8rem;
                }
            }

            /* Mobile cards */
            .card {
                margin-bottom: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .card-header {
                padding: 1rem;
                font-size: 1rem;
                border-bottom: 1px solid #e3e6f0;
            }

            .card-body {
                padding: 1rem;
            }

            /* Mobile tables */
            .table-responsive {
                border: none;
                margin: -1rem;
                margin-top: 0;
                border-radius: 0.5rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-responsive .table {
                margin-bottom: 0;
                font-size: 0.875rem;
            }

            .table th {
                font-size: 0.75rem;
                padding: 0.5rem;
                white-space: nowrap;
            }

            .table td {
                padding: 0.5rem;
                vertical-align: middle;
            }

            /* Mobile forms */
            .form-control, .form-select {
                min-height: 44px;
                font-size: 1rem;
                border-radius: 0.5rem;
            }

            .form-label {
                font-weight: 600;
                margin-bottom: 0.5rem;
                font-size: 0.875rem;
            }

            /* Mobile alerts */
            .alert {
                margin-bottom: 1rem;
                border-radius: 0.5rem;
                padding: 1rem;
                font-size: 0.875rem;
            }

            /* Mobile dropdowns */
            .dropdown-menu {
                border: none;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                border-radius: 0.5rem;
                margin-top: 0.5rem;
                font-size: 0.875rem;
            }

            /* Mobile modals */
            .modal-dialog {
                margin: 0.5rem;
                max-width: none;
                width: calc(100% - 1rem);
            }

            .modal-content {
                border-radius: 0.5rem;
                border: none;
            }

            /* Mobile pagination */
            .pagination {
                justify-content: center;
                margin-top: 1rem;
                flex-wrap: wrap;
            }

            .pagination .page-link {
                padding: 0.5rem 0.75rem;
                min-width: 44px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Mobile dashboard stats */
            .dashboard-card {
                margin-bottom: 1rem;
            }

            .dashboard-stat {
                font-size: 2rem;
            }

            .dashboard-stat-label {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .topbar {
                padding: 0.75rem 1rem;
            }

            .page-content {
                padding: 1rem 0.5rem;
            }

            /* Hide breadcrumb text on very small screens */
            .breadcrumb-item:not(:first-child):not(:last-child) {
                display: none;
            }

            .breadcrumb-item:first-child .fa-home {
                display: inline;
            }

            .breadcrumb-item:first-child span:not(.fa) {
                display: none;
            }

            /* Very small screen adjustments */
            .sidebar {
                width: 100%;
                max-width: 300px;
            }

            .product-btn-mobile {
                min-height: 70px;
                font-size: 0.8rem;
            }

            .dashboard-stat {
                font-size: 1.5rem;
            }

            .card-header {
                padding: 0.75rem;
                font-size: 0.875rem;
            }

            .card-body {
                padding: 0.75rem;
            }

            .table th, .table td {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .form-control, .form-select {
                font-size: 16px; /* Prevents zoom on iOS */
                padding: 0.5rem;
            }
        }

        /* Extra small screens (iPhone SE, etc.) */
        @media (max-width: 375px) {
            .sidebar {
                width: 100%;
            }

            .topbar {
                padding: 0.5rem;
            }

            .sidebar-toggle {
                padding: 0.5rem;
                font-size: 1.25rem;
            }

            .page-content {
                padding: 0.5rem;
            }

            .card {
                margin-bottom: 0.5rem;
            }

            .card-header, .card-body {
                padding: 0.5rem;
            }

            .btn {
                min-height: 40px;
                padding: 0.5rem;
                font-size: 0.75rem;
            }

            .table th, .table td {
                padding: 0.25rem;
                font-size: 0.7rem;
            }
        }

        /* Landscape mobile adjustments */
        @media (max-width: 768px) and (orientation: landscape) {
            .sidebar {
                width: 320px;
            }

            .topbar {
                padding: 0.5rem 1rem;
            }

            .page-content {
                padding: 0.5rem;
            }

            .dashboard-stat {
                font-size: 1.75rem;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn, .sidebar-toggle, .nav-link {
                min-height: 48px;
                min-width: 48px;
            }

            .form-control, .form-select {
                min-height: 48px;
                font-size: 16px;
            }

            .pagination .page-link {
                min-height: 48px;
                min-width: 48px;
            }

            .dropdown-item {
                padding: 0.75rem 1rem;
                min-height: 48px;
                display: flex;
                align-items: center;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .sidebar {
                border-right: 0.5px solid rgba(0, 0, 0, 0.1);
            }

            .card {
                border: 0.5px solid rgba(0, 0, 0, 0.1);
            }
        }
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
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-brand">
            <i class="fas fa-warehouse"></i>
            <span class="sidebar-brand-text">Gestion Stock</span>
        </a>
        
        <nav class="sidebar-menu">
            <!-- Dashboard -->
            <div class="sidebar-item">
                <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.dashboard')); ?></span>
                </a>
            </div>

            <!-- Produits -->
            <?php if(canManageProduits()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('produits.index')); ?>" class="sidebar-link <?php echo e(request()->is('produits*') ? 'active' : ''); ?>">
                    <i class="fas fa-box"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.products')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Gestion Stock -->
            <?php if(canManageEntreesStock()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('entrees-stock.index')); ?>" class="sidebar-link <?php echo e(request()->is('entrees-stock*') ? 'active' : ''); ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.stock_entries')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Fournisseurs -->
            <?php if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('fournisseurs.index')); ?>" class="sidebar-link <?php echo e(request()->is('fournisseurs*') ? 'active' : ''); ?>">
                    <i class="fas fa-truck"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.suppliers')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Commandes -->
            <?php if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('orders.index')); ?>" class="sidebar-link <?php echo e(request()->is('orders*') ? 'active' : ''); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.orders')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Partenaires -->
            <?php if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('partenaires.index')); ?>" class="sidebar-link <?php echo e(request()->is('partenaires*') ? 'active' : ''); ?>">
                    <i class="fas fa-handshake"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.partners')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Transferts -->
            <?php if(canManageTransferts()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('transferts.index')); ?>" class="sidebar-link <?php echo e(request()->is('transferts*') ? 'active' : ''); ?>">
                    <i class="fas fa-exchange-alt"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.transfers')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Ventes -->
            <?php if(canManageVentes()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('ventes.index')); ?>" class="sidebar-link <?php echo e(request()->is('ventes*') ? 'active' : ''); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.sales')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Clients -->
            <?php if(canManageVentes()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('clients.index')); ?>" class="sidebar-link <?php echo e(request()->is('clients*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.clients')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Crédits -->
            <?php if(canManageVentes()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('credits.index')); ?>" class="sidebar-link <?php echo e(request()->is('credits*') ? 'active' : ''); ?>">
                    <i class="fas fa-credit-card"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.credits')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Caisse (POS) -->
            <?php if(auth()->user()->isVendeur()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('pos.index')); ?>" class="sidebar-link <?php echo e(request()->is('pos*') ? 'active' : ''); ?>">
                    <i class="fas fa-cash-register"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.pos')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Gestion des Caisses (Admin/Gestionnaire) -->
            <?php if(auth()->user()->isAdmin() || auth()->user()->isGestionnaire()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('pos.index')); ?>" class="sidebar-link <?php echo e(request()->is('pos*') ? 'active' : ''); ?>">
                    <i class="fas fa-cash-register"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.pos')); ?> <?php echo e(__('messages.management')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Rapports -->
            <?php if(canManageRapports()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('rapports.index')); ?>" class="sidebar-link <?php echo e(request()->is('rapports*') ? 'active' : ''); ?>">
                    <i class="fas fa-file-alt"></i>
                    <span class="sidebar-link-text"><?php echo e(__('messages.reports')); ?></span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Séparateur -->
            <hr class="text-white-50 mx-3 my-2">

            <!-- Administration -->
            <?php if(auth()->user()->isAdmin()): ?>
            <div class="sidebar-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-link <?php echo e(request()->is('admin*') ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-link-text">Dashboard Admin</span>
                </a>
            </div>
            
            <!-- Gestion des utilisateurs -->
            <div class="sidebar-item">
                <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link <?php echo e(request()->is('users*') ? 'active' : ''); ?>">
                    <i class="fas fa-users-cog"></i>
                    <span class="sidebar-link-text">Gestion Utilisateurs</span>
                </a>
            </div>
            
            <!-- Gestion des magasins -->
            <div class="sidebar-item">
                <a href="<?php echo e(route('magasins.index')); ?>" class="sidebar-link <?php echo e(request()->is('magasins*') ? 'active' : ''); ?>">
                    <i class="fas fa-building"></i>
                    <span class="sidebar-link-text">Gestion Magasins</span>
                </a>
            </div>
            
            <!-- Gestion des boutiques -->
            <div class="sidebar-item">
                <a href="<?php echo e(route('boutiques.index')); ?>" class="sidebar-link <?php echo e(request()->is('boutiques*') ? 'active' : ''); ?>">
                    <i class="fas fa-store-alt"></i>
                    <span class="sidebar-link-text">Gestion Boutiques</span>
                </a>
            </div>
            <?php endif; ?>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar();">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="ms-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-decoration-none">
                                <i class="fas fa-home"></i> Accueil
                            </a>
                        </li>
                        <?php echo $__env->yieldContent('breadcrumb'); ?>
                    </ol>
                </nav>
            </div>

            <div class="topbar-right">
                <!-- Language Switcher -->
                <div class="dropdown">
                    <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown" title="<?php echo e(__('messages.language')); ?>">
                        <i class="fas fa-globe"></i>
                        <span class="ms-1"><?php echo e(strtoupper(app()->getLocale())); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?php echo e(__('messages.language')); ?></h6></li>
                        <li>
                            <a class="dropdown-item <?php echo e(app()->getLocale() === 'fr' ? 'active' : ''); ?>" 
                               href="<?php echo e(route('language.switch', 'fr')); ?>">
                                <i class="fas fa-flag me-2"></i> <?php echo e(__('messages.french')); ?>

                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(app()->getLocale() === 'tr' ? 'active' : ''); ?>" 
                               href="<?php echo e(route('language.switch', 'tr')); ?>">
                                <i class="fas fa-flag me-2"></i> <?php echo e(__('messages.turkish')); ?>

                            </a>
                        </li>
                    </ul>
                </div>

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
                            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?>

                        </div>
                        <div class="user-info-text">
                            <div class="fw-semibold"><?php echo e(auth()->user()->name); ?></div>
                            <small class="text-muted"><?php echo e(auth()->user()->role); ?></small>
                        </div>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?php echo e(auth()->user()->name); ?></h6></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user me-2"></i> Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Paramètres
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
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
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-warning alert-dismissible fade show fade-in" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreurs :</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <?php if (! empty(trim($__env->yieldContent('header')))): ?>
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <?php echo $__env->yieldContent('header'); ?>
                </div>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="fade-in">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="text-center">
                <small>
                    &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'Gestion Stock')); ?>. 
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
        // Mobile and touch optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // Detect mobile device
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            
            if (isMobile || isTouch) {
                document.body.classList.add('mobile-device');
                document.body.classList.add('touch-device');
            }
            
            // Prevent double-tap zoom on iOS
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function (event) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);
            
            // Handle orientation change
            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    // Recalculate layouts after orientation change
                    const sidebar = document.getElementById('sidebar');
                    const sidebarOverlay = document.getElementById('sidebarOverlay');
                    
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }, 100);
            });
            
            // Optimize scrolling for mobile
            if (isTouch) {
                document.body.style.touchAction = 'pan-y';
                
                // Disable hover effects on touch devices
                let touchTimer;
                document.addEventListener('touchstart', function() {
                    clearTimeout(touchTimer);
                    document.body.classList.add('touching');
                });
                
                document.addEventListener('touchend', function() {
                    touchTimer = setTimeout(function() {
                        document.body.classList.remove('touching');
                    }, 500);
                });
            }
            
            // Improve button feedback on mobile
            if (isMobile) {
                document.querySelectorAll('.btn, .sidebar-toggle, .nav-link').forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.95)';
                    });
                    
                    button.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            }
            
            // Handle safe area for notched screens
            if (CSS.supports('padding-top', 'env(safe-area-inset-top)')) {
                const safeArea = getComputedStyle(document.body);
                document.documentElement.style.setProperty('--safe-area-inset-top', safeArea.getPropertyValue('env(safe-area-inset-top)'));
                document.documentElement.style.setProperty('--safe-area-inset-bottom', safeArea.getPropertyValue('env(safe-area-inset-bottom)'));
            }
        });

        // Translation helper
        window.t = function(key, params = {}) {
            const translations = <?php echo json_encode(__('messages'), 15, 512) ?>;
            let text = key.split('.').reduce((obj, i) => obj && obj[i], translations) || key;
            
            // Replace parameters in text
            Object.keys(params).forEach(param => {
                text = text.replace(new RegExp(`_${param}_`, 'g'), params[param]);
            });
            
            return text;
        }

        // Simple mobile menu toggle
        function toggleSidebar() {
            console.log('toggleSidebar called');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && sidebarOverlay) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                console.log('Sidebar toggled, show:', sidebar.classList.contains('show'));
            } else {
                console.error('Sidebar elements not found');
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Click event triggered');
                    toggleSidebar();
                });
                console.log('Event listener attached to sidebar toggle');
            } else {
                console.error('Sidebar toggle button not found');
            }

            // Close sidebar when clicking overlay
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    console.log('Overlay clicked');
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Close sidebar when clicking links (mobile)
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.querySelectorAll('.sidebar-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('show');
                            sidebarOverlay.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    });
                });
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
                    search: "<?php echo e(__('messages.rechercher')); ?>:",
                    lengthMenu: "<?php echo e(__('messages.afficher')); ?> _MENU_ <?php echo e(__('messages.elements')); ?>",
                    info: "<?php echo e(__('messages.affichage_de_a_sur')); ?>",
                    paginate: {
                        first: "<?php echo e(__('messages.premier')); ?>",
                        last: "<?php echo e(__('messages.dernier')); ?>",
                        next: "<?php echo e(__('messages.suivant')); ?>",
                        previous: "<?php echo e(__('messages.precedent')); ?>"
                    },
                    emptyTable: "<?php echo e(__('messages.aucune_donnee')); ?>",
                    zeroRecords: "<?php echo e(__('messages.aucune_donnee')); ?>",
                    infoEmpty: "<?php echo e(__('messages.aucune_donnee')); ?>",
                    infoFiltered: "(<?php echo e(__('messages.filtrer_sur')); ?>)",
                    loadingRecords: "<?php echo e(__('messages.chargement_en_cours')); ?>",
                    processing: "<?php echo e(__('messages.traitement_en_cours')); ?>...",
                    aria: {
                        sortAscending: ": <?php echo e(__('messages.ordonner')); ?> <?php echo e(__('messages.par_ordre_croissant')); ?>",
                        sortDescending: ": <?php echo e(__('messages.ordonner')); ?> <?php echo e(__('messages.par_ordre_decroissant')); ?>"
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
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?php echo e(__("messages.traitement_en_cours")); ?>...';
                }
            });
        });

        // Confirm modals
        function confirmAction(message, callback) {
            if (confirm(message || "<?php echo e(__('messages.confirmer_action')); ?>")) {
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

        // Mobile sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            console.log('Mobile menu initialization:', {
                sidebarToggle: !!sidebarToggle,
                sidebar: !!sidebar,
                sidebarOverlay: !!sidebarOverlay,
                mainContent: !!mainContent
            });

            // Toggle sidebar on mobile
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Toggle clicked');
                    
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');

                    // Prevent body scroll when sidebar is open
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                });

                // Close sidebar when clicking overlay
                sidebarOverlay.addEventListener('click', function() {
                    console.log('Overlay clicked');
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                });

                // Close sidebar when clicking a link (on mobile)
                sidebar.querySelectorAll('.sidebar-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('show');
                            sidebarOverlay.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    });
                });
            } else {
                console.error('Mobile menu elements not found');
            }

            // Close sidebar on window resize if desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/layouts/app.blade.php ENDPATH**/ ?>