<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — {{ config('app.name', 'GestionMagasin') }}</title>
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

        .input-style {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
            transition: all 0.2s ease;
        }

        .input-style:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            outline: none;
        }

        .input-style::placeholder { color: #64748b; }

        .plan-card {
            transition: all 0.25s ease;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .plan-card:hover { transform: translateY(-4px); }

        .plan-card.selected {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.15);
        }

        .btn-shine {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
        .btn-shine:hover { box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4); transform: translateY(-2px); }

        .custom-check {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 50%;
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <nav class="bg-slate-900/50 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <i class="fas fa-store text-blue-400 text-2xl mr-2"></i>
                    <span class="font-display font-bold text-xl text-white">{{ config('app.name', 'GestionMagasin') }}</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pricing') }}" class="text-gray-300 hover:text-white transition-colors hidden sm:inline">Tarifs</a>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-colors">Connexion</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Intro -->
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl md:text-5xl font-bold mb-4">
                Créez votre <span class="gradient-text">compte gratuit</span>
            </h1>
            <p class="text-gray-400 max-w-xl mx-auto">
                {{ config('plans.trial_days', 14) }} jours d'essai gratuit, sans carte bancaire. Choisissez votre plan ci-dessous.
            </p>
        </div>

        <!-- Plan Selection -->
        <div class="grid md:grid-cols-3 gap-4 mb-10">
            @foreach(config('plans') as $key => $plan)
                @if(is_array($plan) && isset($plan['name']))
                <div class="plan-card glass-card rounded-2xl p-6 {{ (old('plan') ?? 'starter') === $key ? 'selected' : '' }}"
                     onclick="selectPlan('{{ $key }}')" id="plan-{{ $key }}">
                    @if($key === 'pro')
                        <div class="text-xs font-semibold text-blue-300 bg-blue-500/20 border border-blue-500/30 rounded-full px-3 py-1 inline-block mb-3">
                            <i class="fas fa-star mr-1"></i>Plus populaire
                        </div>
                    @endif
                    <h3 class="font-display text-xl font-bold text-white mb-1">{{ $plan['name'] }}</h3>
                    <p class="text-gray-400 text-sm mb-4">{{ $plan['description'] }}</p>
                    <p class="mb-4">
                        <span class="text-3xl font-bold gradient-text font-display">{{ number_format($plan['price'], 0, ',', ' ') }}</span>
                        <span class="text-sm text-gray-400">{{ config('plans.currency_symbol') }}/mois</span>
                    </p>
                    <ul class="space-y-2 text-left">
                        @foreach(array_slice($plan['feature_list'] ?? [], 0, 5) as $feature)
                        <li class="flex items-center gap-2">
                            <span class="custom-check"><i class="fas fa-check text-white text-[10px]"></i></span>
                            <span class="text-gray-300 text-sm">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Trial info banner -->
        <div id="trial-banner" class="mb-6 bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-clock text-blue-400 mt-0.5"></i>
            <p class="text-blue-200 text-sm">
                Essai gratuit de {{ config('plans.trial_days', 14) }} jours sur le plan sélectionné. Vous pourrez changer de plan à tout moment depuis votre tableau de bord.
            </p>
        </div>

        <!-- Registration Form -->
        <form action="{{ route('saas.register') }}" method="POST" class="glass-card rounded-2xl p-8">
            @csrf
            <input type="hidden" name="plan" id="selected-plan" value="{{ old('plan') ?? 'starter' }}">

            @if(session('error'))
            <div class="bg-red-900/30 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
            @endif

            <!-- Company Information -->
            <div class="mb-8">
                <h3 class="font-display text-lg font-semibold text-white mb-4">Informations de l'entreprise</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom de l'entreprise</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Votre entreprise" required>
                        @error('company_name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="+225 07 00 00 00 00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Adresse</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Rue, quartier">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Ville</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Abidjan">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Code postal</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                               class="input-style w-full md:w-1/2 px-4 py-3 rounded-xl"
                               placeholder="00225">
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="mb-8">
                <h3 class="font-display text-lg font-semibold text-white mb-4">Informations personnelles</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Jean Dupont" required autofocus autocomplete="name">
                        @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="jean@exemple.com" required autocomplete="username">
                        @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Mot de passe</label>
                        <input type="password" name="password"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="••••••••" required autocomplete="new-password">
                        @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation"
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="••••••••" required autocomplete="new-password">
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="terms" required
                           class="mt-1 w-4 h-4 rounded border-gray-600 text-blue-500 focus:ring-blue-500 bg-gray-800">
                    <span class="text-sm text-gray-400">
                        J'accepte les conditions d'utilisation et la politique de confidentialité
                    </span>
                </label>
            </div>

            <button type="submit" class="btn-shine w-full py-4 px-4 rounded-xl text-white font-semibold text-lg">
                <span id="submit-text">Démarrer mon essai gratuit</span>
            </button>

            <p class="text-center mt-6 text-gray-400">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="text-blue-400 hover:underline font-medium">Connectez-vous</a>
            </p>
        </form>

        <p class="text-center mt-8 text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'GestionMagasin') }}. Tous droits réservés.
        </p>
    </div>

    <script>
        function selectPlan(plan) {
            document.querySelectorAll('.plan-card').forEach(card => card.classList.remove('selected'));
            document.getElementById('plan-' + plan).classList.add('selected');
            document.getElementById('selected-plan').value = plan;

            const submitText = document.getElementById('submit-text');
            submitText.textContent = plan === 'starter'
                ? "Démarrer mon essai gratuit"
                : "Créer mon compte " + document.getElementById('plan-' + plan).querySelector('h3').textContent;
        }

        selectPlan('{{ old('plan') ?? 'starter' }}');
    </script>
</body>
</html>
