<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarifs - Gestion de Magasin SaaS</title>
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
        
        /* Animated gradient mesh background */
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
        
        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Glassmorphism cards */
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
        
        /* Pro card glow effect */
        .pro-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Custom checkmark */
        .custom-check {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Button shine effect */
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
        
        /* Icon containers */
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
        
        /* FAQ accordion */
        .faq-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            background: rgba(255, 255, 255, 0.02);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-answer.open {
            max-height: 200px;
        }
        
        /* Footer glow */
        .footer-logo:hover {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        
        /* Toggle switch */
        .toggle-switch {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            padding: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .toggle-switch:hover {
            border-color: rgba(59, 130, 246, 0.5);
        }
        
        .toggle-dot {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 9999px;
            transition: transform 0.3s ease;
        }
        
        /* Scroll animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <nav class="bg-slate-900/50 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-store text-blue-400 text-2xl mr-2"></i>
                    <span class="font-display font-bold text-xl text-white">GestionMagasin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/login" class="text-gray-300 hover:text-white transition-colors">Connexion</a>
                    <a href="{{ route('saas.register') }}" class="btn-shine bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all">
                        S'inscrire
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="gradient-mesh py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="font-display text-5xl md:text-7xl font-bold mb-6">
                Choisissez le plan qui <span class="gradient-text">vous convient</span>
            </h1>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                Commencez gratuitement, annulez à tout moment. Sans engagement.
            </p>
            <div class="flex justify-center items-center space-x-6">
                <span class="text-gray-300 flex items-center">
                    <i class="fas fa-clock text-blue-400 mr-2"></i>
                    Essai gratuit de 14 jours
                </span>
                <span class="bg-gradient-to-r from-blue-600/20 to-blue-500/20 border border-blue-500/30 px-4 py-2 rounded-full text-sm text-blue-300 backdrop-blur-sm">
                    <i class="fas fa-credit-card mr-2"></i>
                    Sans carte bancaire
                </span>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <!-- Toggle -->
        <div class="flex justify-center items-center mb-12">
            <span class="text-gray-300 mr-4">Mensuel</span>
            <div class="toggle-switch w-16 h-8 flex items-center" onclick="togglePricing()">
                <div class="toggle-dot w-6 h-6" id="toggleDot"></div>
            </div>
            <span class="text-gray-300 ml-4">Annuel <span class="text-blue-400 text-sm">(2 mois offerts)</span></span>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $key => $plan)
                @if($key !== 'currency' && $key !== 'currency_symbol' && $key !== 'trial_days' && $key !== 'warning_days' && $key !== 'redirect_expired' && $key !== 'redirect_cancelled')
                <div class="{{ $key === 'pro' ? 'pro-card' : 'glass-card' }} rounded-2xl overflow-hidden fade-up" data-delay="{{ $loop->index * 100 }}">
                    @if($key === 'pro')
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white text-center py-3 text-sm font-semibold">
                        <i class="fas fa-star mr-2"></i>Plus populaire
                    </div>
                    @endif
                    
                    <div class="p-8">
                        <h3 class="font-display text-2xl font-bold text-white mb-2">{{ $plan['name'] }}</h3>
                        <p class="text-gray-400 mb-6">{{ $plan['description'] }}</p>
                        
                        <div class="mb-6">
                            <span class="text-5xl font-bold text-white font-display" id="price-{{ $key }}">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                            <span class="text-gray-400 ml-2">{{ config('plans.currency_symbol') }}/mois</span>
                            @if(isset($plan['price_yearly']))
                            <p class="text-sm text-gray-500 mt-2 yearly-price hidden">
                                ou {{ number_format($plan['price_yearly'], 0, ',', ' ') }}{{ config('plans.currency_symbol') }}/an (2 mois offerts)
                            </p>
                            @endif
                        </div>

                        <ul class="space-y-4 mb-8">
                            @foreach($plan['feature_list'] as $feature)
                            <li class="flex items-center text-gray-300">
                                <div class="custom-check mr-3">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('saas.register') }}?plan={{ $key }}" 
                           class="btn-shine block w-full py-4 px-4 rounded-lg text-center font-semibold
                                  {{ $key === 'pro' 
                                      ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400' 
                                      : 'bg-white/10 text-white hover:bg-white/20 border border-white/20' }} transition-all">
                            Commencer l'essai gratuit
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-4xl font-bold text-center text-white mb-16">
                Pourquoi choisir <span class="gradient-text">GestionMagasin</span> ?
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center fade-up" data-delay="0">
                    <div class="icon-container w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-rocket text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Déploiement rapide</h3>
                    <p class="text-gray-400">Commencez en quelques minutes, pas besoin d'installation complexe</p>
                </div>
                <div class="text-center fade-up" data-delay="100">
                    <div class="icon-container w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Sécurité garantie</h3>
                    <p class="text-gray-400">Vos données sont protégées avec les meilleurs standards de sécurité</p>
                </div>
                <div class="text-center fade-up" data-delay="200">
                    <div class="icon-container w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="font-display text-xl font-semibold mb-3 text-white">Support dédié</h3>
                    <p class="text-gray-400">Notre équipe est disponible pour vous aider à tout moment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-20 relative">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-4xl font-bold text-center text-white mb-16">
                Questions fréquentes
            </h2>
            <div class="space-y-4">
                <div class="faq-item py-6 cursor-pointer" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-white text-lg">Puis-je annuler mon abonnement à tout moment ?</h3>
                        <i class="fas fa-chevron-down text-blue-400 transition-transform"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-400 mt-4">Oui, vous pouvez annuler votre abonnement à tout moment. Aucun engagement.</p>
                    </div>
                </div>
                <div class="faq-item py-6 cursor-pointer" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-white text-lg">L'essai gratuit est-il vraiment gratuit ?</h3>
                        <i class="fas fa-chevron-down text-blue-400 transition-transform"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-400 mt-4">Absolument ! Aucune carte bancaire requise pour commencer l'essai de 14 jours.</p>
                    </div>
                </div>
                <div class="faq-item py-6 cursor-pointer" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-white text-lg">Puis-je changer de plan plus tard ?</h3>
                        <i class="fas fa-chevron-down text-blue-400 transition-transform"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-400 mt-4">Oui, vous pouvez upgrader ou downgrader votre plan à tout moment depuis votre dashboard.</p>
                    </div>
                </div>
                <div class="faq-item py-6 cursor-pointer" onclick="toggleFaq(this)">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-white text-lg">Mes données sont-elles sécurisées ?</h3>
                        <i class="fas fa-chevron-down text-blue-400 transition-transform"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-400 mt-4">Oui, nous utilisons les mêmes standards de sécurité que les banques pour protéger vos données.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900/80 backdrop-blur-lg border-t border-white/10 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12">
                <div>
                    <div class="flex items-center mb-6">
                        <i class="fas fa-store text-blue-400 text-2xl mr-3"></i>
                        <span class="font-display font-bold text-xl text-white footer-logo transition-all">GestionMagasin</span>
                    </div>
                    <p class="text-gray-400">La solution complète pour gérer votre magasin en ligne.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Produit</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Fonctionnalités</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Intégrations</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Support</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Statut système</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white">Légal</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Conditions</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Confidentialité</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">CGU</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-12 pt-8 text-center text-gray-500">
                <p>&copy; {{ date('Y') }} GestionMagasin. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle pricing monthly/yearly
        let isYearly = false;
        const prices = {
            starter: { monthly: {{ $plans['starter']['price'] }}, yearly: {{ $plans['starter']['price_yearly'] ?? 0 }} },
            pro: { monthly: {{ $plans['pro']['price'] }}, yearly: {{ $plans['pro']['price_yearly'] ?? 0 }} },
            enterprise: { monthly: {{ $plans['enterprise']['price'] }}, yearly: {{ $plans['enterprise']['price_yearly'] ?? 0 }} }
        };

        function togglePricing() {
            isYearly = !isYearly;
            const toggleDot = document.getElementById('toggleDot');
            
            if (isYearly) {
                toggleDot.style.transform = 'translateX(32px)';
                // Update prices to yearly
                Object.keys(prices).forEach(key => {
                    const priceElement = document.getElementById(`price-${key}`);
                    if (priceElement) {
                        priceElement.textContent = numberFormat(prices[key].yearly);
                    }
                });
                // Show yearly pricing text
                document.querySelectorAll('.yearly-price').forEach(el => el.classList.remove('hidden'));
            } else {
                toggleDot.style.transform = 'translateX(0)';
                // Update prices to monthly
                Object.keys(prices).forEach(key => {
                    const priceElement = document.getElementById(`price-${key}`);
                    if (priceElement) {
                        priceElement.textContent = numberFormat(prices[key].monthly);
                    }
                });
                // Hide yearly pricing text
                document.querySelectorAll('.yearly-price').forEach(el => el.classList.add('hidden'));
            }
        }

        function numberFormat(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // FAQ accordion
        function toggleFaq(element) {
            const answer = element.querySelector('.faq-answer');
            const icon = element.querySelector('i');
            
            answer.classList.toggle('open');
            icon.style.transform = answer.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
        }

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, delay);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-up').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
