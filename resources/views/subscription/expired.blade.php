<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement expiré — {{ config('app.name', 'GestionMagasin') }}</title>
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
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 50%, var(--navy-light) 100%);
            min-height: 100vh;
        }
        .font-display { font-family: 'Syne', sans-serif; }
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
        }
        .plan-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.25s ease;
        }
        .plan-card.highlight {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
        }
        .plan-card:hover { transform: translateY(-4px); }
        .btn-shine {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-shine:hover::before { left: 100%; }
    </style>
</head>
<body class="text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-red-500/15 border border-red-500/30 mb-6">
                <i class="fas fa-exclamation-circle text-red-400 text-3xl"></i>
            </div>
            <h1 class="font-display text-4xl font-bold mb-3">Abonnement expiré</h1>
            <p class="text-gray-400 max-w-xl mx-auto">Votre période d'essai ou votre abonnement est arrivé à son terme. Choisissez un plan pour continuer à utiliser {{ config('app.name', 'GestionMagasin') }}.</p>
        </div>

        @if($tenant)
        <div class="glass-card rounded-2xl p-6 mb-10">
            <h3 class="font-display text-lg font-semibold text-white mb-4">Informations de votre compte</h3>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Entreprise</p>
                    <p class="font-semibold text-white">{{ $tenant->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Plan actuel</p>
                    <p class="font-semibold text-white">{{ ucfirst($tenant->plan) }}</p>
                </div>
                @if($tenant->subscription_ends_at)
                <div>
                    <p class="text-gray-400">Expiration</p>
                    <p class="font-semibold text-red-400">{{ $tenant->subscription_ends_at->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <h2 class="font-display text-2xl font-bold text-center mb-8">Choisissez un plan pour continuer</h2>

        <div class="grid md:grid-cols-3 gap-6 mb-10">
            @foreach($plans as $key => $plan)
                @if(is_array($plan) && isset($plan['name']))
                <div class="plan-card rounded-2xl p-6 {{ $key === 'pro' ? 'highlight' : '' }}">
                    @if($key === 'pro')
                    <div class="text-center mb-3">
                        <span class="bg-blue-500/20 border border-blue-500/30 text-blue-300 px-3 py-1 rounded-full text-xs font-semibold">
                            <i class="fas fa-star mr-1"></i>Recommandé
                        </span>
                    </div>
                    @endif

                    <h3 class="font-display text-xl font-bold text-center text-white mb-2">{{ $plan['name'] }}</h3>
                    <div class="text-center mb-5">
                        <span class="text-3xl font-bold gradient-text font-display">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                        <span class="text-gray-400 text-sm">{{ config('plans.currency_symbol') }}/mois</span>
                    </div>

                    <ul class="space-y-2 mb-6 text-sm">
                        @foreach(array_slice($plan['feature_list'] ?? [], 0, 5) as $feature)
                        <li class="flex items-center gap-2 text-gray-300">
                            <i class="fas fa-check text-blue-400"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('subscription.show') }}"
                       class="btn-shine block w-full py-3 px-4 rounded-lg text-center font-semibold
                              {{ $key === 'pro'
                                  ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400'
                                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/20' }}">
                        Choisir {{ $plan['name'] }}
                    </a>
                </div>
                @endif
            @endforeach
        </div>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Se déconnecter
                </button>
            </form>
        </div>

        <p class="text-center mt-10 text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'GestionMagasin') }}. Tous droits réservés.
        </p>
    </div>
</body>
</html>
