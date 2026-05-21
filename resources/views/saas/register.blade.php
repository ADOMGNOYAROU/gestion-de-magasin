<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - GestionMagasin SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #0f1117 0%, #1a1d2d 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(22, 27, 34, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-style {
            background: rgba(15, 17, 23, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
            transition: all 0.3s ease;
        }
        .input-style:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            outline: none;
        }
        .input-style::placeholder {
            color: #64748b;
        }
        .plan-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        .plan-card:hover {
            transform: translateY(-4px);
        }
        .plan-card.selected {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
        }
        .plan-card.free {
            border-color: rgba(34, 197, 94, 0.3);
        }
        .plan-card.free.selected {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
        }
        .limitation-badge {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
        }
        .feature-check {
            color: #22c55e;
        }
        .feature-cross {
            color: #ef4444;
        }
        .upgrade-highlight {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
            border: 1px solid rgba(139, 92, 246, 0.5);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-5xl">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Choisissez votre plan</h1>
            <p class="text-gray-400">Commencez gratuitement ou passez aux fonctionnalités avancées</p>
        </div>

        <!-- Plan Selection -->
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            @foreach(config('plans') as $key => $plan)
                @if($key !== 'currency' && $key !== 'currency_symbol' && $key !== 'trial_days' && $key !== 'warning_days' && $key !== 'redirect_expired' && $key !== 'redirect_cancelled')
                <div class="plan-card {{ $key === 'starter' ? 'free' : '' }} glass-card rounded-2xl p-6 {{ old('plan') === $key || $key === 'starter' ? 'selected' : '' }}" onclick="selectPlan('{{ $key }}')" id="plan-{{ $key }}">
                    <div class="text-center">
                        <div class="w-12 h-12 {{ $key === 'starter' ? 'bg-green-500/20' : ($key === 'pro' ? 'bg-purple-500/20' : 'bg-indigo-500/20') }} rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 {{ $key === 'starter' ? 'text-green-400' : ($key === 'pro' ? 'text-purple-400' : 'text-indigo-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($key === 'starter')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @elseif($key === 'pro')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                @endif
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">{{ $plan['name'] }}</h3>
                        <p class="text-3xl font-bold {{ $key === 'starter' ? 'text-green-400' : ($key === 'pro' ? 'text-purple-400' : 'text-indigo-400') }} mb-2">{{ $plan['price'] }}€<span class="text-sm text-gray-400">/mois</span></p>
                        <p class="text-gray-400 text-sm mb-4">{{ $plan['description'] }}</p>
                        
                        <div class="text-left space-y-2 mb-4">
                            @if($key === 'starter')
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">1 magasin</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">100 produits</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">Support email</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-cross" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-500 text-sm line-through">Rapports avancés</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-cross" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-500 text-sm line-through">Multi-magasins</span>
                                </div>
                                <div class="limitation-badge rounded-lg p-2 text-center mt-3">
                                    <p class="text-red-400 text-xs font-semibold">⚠️ Limité à 14 jours</p>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">{{ $key === 'pro' ? '5 magasins' : 'Magasins illimités' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">Produits illimités</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">{{ $key === 'pro' ? 'Support prioritaire' : 'Support dédié 24/7' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">Rapports avancés</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 feature-check" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-300 text-sm">{{ $key === 'pro' ? 'Export de données' : 'API personnalisée' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Limitations Warning for Free Plan -->
        <div id="free-limitations" class="hidden mb-6 upgrade-highlight rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-white font-semibold mb-2">Limitations de l'essai gratuit</h4>
                    <ul class="text-gray-300 text-sm space-y-1">
                        <li>• Accès limité à 1 magasin uniquement</li>
                        <li>• Maximum 100 produits</li>
                        <li>• Pas de rapports avancés ni d'export de données</li>
                        <li>• Support par email uniquement (délai 48h)</li>
                        <li>• Après 14 jours, compte suspendu</li>
                    </ul>
                    <p class="text-purple-400 text-sm mt-2 font-medium">💡 Passez au plan Pro pour débloquer toutes les fonctionnalités !</p>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <form action="{{ route('saas.register') }}" method="POST" class="glass-card rounded-2xl p-8">
            @csrf
            <input type="hidden" name="plan" id="selected-plan" value="{{ old('plan') ?? 'starter' }}">

            @if(session('error'))
            <div class="bg-red-900/30 border border-red-500/50 text-red-400 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
            @endif

            <!-- Company Information (for paid plans) -->
            <div id="company-section" class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Informations de l'entreprise</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom de l'entreprise</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Votre entreprise">
                        @if($errors->has('company_name'))
                        <p class="mt-1 text-sm text-red-400">{{ $errors->first('company_name') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="+33 6 12 34 56 78">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Adresse</label>
                        <input type="text" name="address" value="{{ old('address') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="123 Rue Example">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Ville</label>
                        <input type="text" name="city" value="{{ old('city') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Paris">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Code postal</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                               class="input-style w-full md:w-1/2 px-4 py-3 rounded-xl"
                               placeholder="75001">
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Informations personnelles</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="Jean Dupont" required autofocus autocomplete="name">
                        @if($errors->has('name'))
                        <p class="mt-1 text-sm text-red-400">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="jean@exemple.com" required autocomplete="username">
                        @if($errors->has('email'))
                        <p class="mt-1 text-sm text-red-400">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Mot de passe</label>
                        <input type="password" name="password" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="••••••••" required autocomplete="new-password">
                        @if($errors->has('password'))
                        <p class="mt-1 text-sm text-red-400">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" 
                               class="input-style w-full px-4 py-3 rounded-xl"
                               placeholder="••••••••" required autocomplete="new-password">
                        @if($errors->has('password_confirmation'))
                        <p class="mt-1 text-sm text-red-400">{{ $errors->first('password_confirmation') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="terms" required 
                           class="mt-1 w-4 h-4 rounded border-gray-600 text-purple-500 focus:ring-purple-500 bg-gray-800">
                    <span class="text-sm text-gray-400">
                        J'accepte les <a href="#" class="text-purple-400 hover:underline">conditions d'utilisation</a> et la 
                        <a href="#" class="text-purple-400 hover:underline">politique de confidentialité</a>
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full py-3 px-4 rounded-xl text-white font-semibold text-lg">
                <span id="submit-text">Commencer l'essai gratuit (14 jours)</span>
            </button>

            <!-- Login Link -->
            <p class="text-center mt-6 text-gray-400">
                Déjà inscrit ? 
                <a href="/login" class="text-purple-400 hover:underline font-medium">Connectez-vous</a>
            </p>
        </form>

        <!-- Footer -->
        <p class="text-center mt-8 text-gray-500 text-sm">
            © {{ date('Y') }} GestionMagasin. Tous droits réservés.
        </p>
    </div>

    <script>
        function selectPlan(plan) {
            // Remove selected class from all cards
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            document.getElementById('plan-' + plan).classList.add('selected');

            // Update hidden input
            document.getElementById('selected-plan').value = plan;

            // Show/hide company section based on plan
            const companySection = document.getElementById('company-section');
            const freeLimitations = document.getElementById('free-limitations');
            const submitText = document.getElementById('submit-text');
            const companyNameInput = document.getElementById('company_name');

            if (plan === 'starter') {
                companySection.classList.add('hidden');
                freeLimitations.classList.remove('hidden');
                submitText.textContent = 'Commencer l\'essai gratuit (14 jours)';
                // Auto-fill company name with user's name for free plan
                const nameInput = document.querySelector('input[name="name"]');
                if (nameInput && nameInput.value) {
                    companyNameInput.value = nameInput.value + "'s Business";
                } else {
                    companyNameInput.value = "Mon Entreprise";
                }
            } else {
                companySection.classList.remove('hidden');
                freeLimitations.classList.add('hidden');
                if (plan === 'pro') {
                    submitText.textContent = 'Créer mon compte Pro';
                } else {
                    submitText.textContent = 'Demander un devis Enterprise';
                }
                // Clear auto-filled value if user switches to paid plan
                if (companyNameInput.value === "Mon Entreprise" || companyNameInput.value.endsWith("'s Business")) {
                    companyNameInput.value = '';
                }
            }
        }

        // Initialize with starter plan selected
        selectPlan('{{ old('plan') ?? 'starter' }}');

        // Auto-fill company name when user types their name
        document.querySelector('input[name="name"]').addEventListener('input', function(e) {
            const selectedPlan = document.getElementById('selected-plan').value;
            const companyNameInput = document.getElementById('company_name');
            if (selectedPlan === 'starter') {
                companyNameInput.value = e.target.value + "'s Business";
            }
        });
    </script>
</body>
</html>
