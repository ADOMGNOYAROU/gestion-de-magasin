<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Asime') }} — Gérez vos boutiques, stocks et ventes en un seul endroit</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #0a0e27;
            --navy: #0f172a;
            --navy-light: #1e293b;
            --electric-blue: #3b82f6;
            --electric-blue-light: #60a5fa;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 50%, var(--navy-light) 100%);
            min-height: 100vh;
        }

        .font-display {
            font-family: 'Syne', sans-serif;
        }

        .gradient-mesh {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 50%, var(--navy-light) 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-mesh::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 70%, rgba(96, 165, 250, 0.1) 0%, transparent 50%);
            animation: gradientShift 15s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.25);
        }

        .icon-container {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(96, 165, 250, 0.1) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .icon-container:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(96, 165, 250, 0.2) 100%);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
            transform: scale(1.1);
        }

        .btn-shine {
            position: relative;
            overflow: hidden;
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-shine:hover::before {
            left: 100%;
        }

        .footer-logo:hover {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <nav class="bg-slate-900/50 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <x-application-logo class="w-8 h-8 mr-2" />
                    <span class="font-display font-bold text-xl text-white">{{ config('app.name', 'Asime') }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pricing') }}" class="text-gray-300 hover:text-white transition-colors hidden sm:inline">Tarifs</a>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-colors">Connexion</a>
                    <a href="{{ route('saas.register') }}" class="btn-shine bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all">
                        S'inscrire
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="gradient-mesh py-28 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <span class="inline-block bg-gradient-to-r from-blue-600/20 to-blue-500/20 border border-blue-500/30 px-4 py-2 rounded-full text-sm text-blue-300 backdrop-blur-sm mb-8">
                <i class="fas fa-bolt mr-2"></i>Essai gratuit de {{ config('plans.trial_days', 14) }} jours, sans carte bancaire
            </span>
            <h1 class="font-display text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Gérez vos magasins, <span class="gradient-text">stocks et ventes</span><br class="hidden md:block"> en un seul endroit
            </h1>
            <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">
                Caisse (POS), gestion multi-boutiques, fournisseurs, crédits clients et rapports détaillés
                — tout ce dont votre commerce a besoin, accessible depuis n'importe où.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('saas.register') }}" class="btn-shine bg-gradient-to-r from-blue-600 to-blue-500 text-white px-8 py-4 rounded-lg font-semibold hover:from-blue-500 hover:to-blue-400 transition-all w-full sm:w-auto text-center">
                    Démarrer gratuitement <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('pricing') }}" class="bg-white/10 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 border border-white/20 transition-all w-full sm:w-auto text-center">
                    Voir les tarifs
                </a>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-4xl font-bold text-center text-white mb-4">
                Tout pour piloter votre <span class="gradient-text">commerce</span>
            </h2>
            <p class="text-gray-400 text-center max-w-2xl mx-auto mb-16">
                Une seule plateforme pour vos boutiques, votre stock et vos équipes.
            </p>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass-card rounded-2xl p-8 text-center">
                    <div class="icon-container w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cash-register text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Point de vente (POS)</h3>
                    <p class="text-gray-400">Encaissez en boutique, gérez vos sessions de caisse et imprimez vos tickets en quelques secondes.</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center">
                    <div class="icon-container w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-boxes text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Stock multi-boutiques</h3>
                    <p class="text-gray-400">Suivez vos stocks en temps réel, gérez les transferts entre magasins et boutiques, et vos commandes fournisseurs.</p>
                </div>
                <div class="glass-card rounded-2xl p-8 text-center">
                    <div class="icon-container w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Rapports & crédits clients</h3>
                    <p class="text-gray-400">Rapports de ventes exportables, suivi des crédits clients et tableaux de bord par rôle (admin, gestionnaire, vendeur).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center glass-card rounded-3xl p-12 float">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                Prêt à organiser votre commerce ?
            </h2>
            <p class="text-gray-300 mb-8 max-w-xl mx-auto">
                Créez votre compte en moins de 2 minutes et profitez de {{ config('plans.trial_days', 14) }} jours d'essai gratuit, sans engagement.
            </p>
            <a href="{{ route('saas.register') }}" class="btn-shine inline-block bg-gradient-to-r from-blue-600 to-blue-500 text-white px-8 py-4 rounded-lg font-semibold hover:from-blue-500 hover:to-blue-400 transition-all">
                Créer mon compte gratuitement
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900/80 backdrop-blur-lg border-t border-white/10 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center mb-6">
                        <x-application-logo class="w-8 h-8 mr-3" />
                        <span class="font-display font-bold text-xl text-white footer-logo transition-all">{{ config('app.name', 'Asime') }}</span>
                    </div>
                    <p class="text-gray-400">La solution complète pour gérer votre magasin en ligne.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Produit</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="{{ route('saas.register') }}" class="hover:text-white transition-colors">S'inscrire</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Connexion</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Support</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Légal</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Conditions</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Confidentialité</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-12 pt-8 text-center text-gray-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Asime') }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
