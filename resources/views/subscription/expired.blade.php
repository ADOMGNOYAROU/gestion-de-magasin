<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement Expiré - Gestion de Magasin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full mx-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-red-600 text-white py-8 px-8 text-center">
                <i class="fas fa-exclamation-circle text-6xl mb-4"></i>
                <h1 class="text-3xl font-bold mb-2">Abonnement Expiré</h1>
                <p class="text-red-100">Votre abonnement a expiré. Veuillez le renouveler pour continuer.</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                @if($tenant)
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Informations de votre compte</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Entreprise:</p>
                            <p class="font-semibold">{{ $tenant->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Plan actuel:</p>
                            <p class="font-semibold">{{ ucfirst($tenant->plan) }}</p>
                        </div>
                        @if($tenant->subscription_ends_at)
                        <div>
                            <p class="text-gray-600">Expiration:</p>
                            <p class="font-semibold text-red-600">{{ $tenant->subscription_ends_at->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <h2 class="text-2xl font-bold text-center mb-6">Choisissez un plan pour continuer</h2>

                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    @foreach($plans as $key => $plan)
                        @if($key !== 'currency' && $key !== 'currency_symbol' && $key !== 'trial_days' && $key !== 'warning_days' && $key !== 'redirect_expired' && $key !== 'redirect_cancelled')
                        <div class="border-2 rounded-xl p-6 {{ $key === 'pro' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            @if($key === 'pro')
                            <div class="text-center mb-4">
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm">Recommandé</span>
                            </div>
                            @endif
                            
                            <h3 class="text-xl font-bold text-center mb-2">{{ $plan['name'] }}</h3>
                            <div class="text-center mb-4">
                                <span class="text-4xl font-bold">{{ $plan['price'] }}</span>
                                <span class="text-gray-600">€/mois</span>
                            </div>

                            <ul class="space-y-2 mb-6 text-sm">
                                @foreach($plan['feature_list'] as $feature)
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>

                            <a href="{{ route('subscription.show') }}" 
                               class="block w-full py-3 px-4 rounded-lg text-center font-semibold
                                      {{ $key === 'pro' 
                                          ? 'bg-blue-600 text-white hover:bg-blue-700' 
                                          : 'bg-gray-200 text-gray-900 hover:bg-gray-300' }}">
                                Choisir {{ $plan['name'] }}
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="text-center">
                    <a href="/logout" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Se déconnecter
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p>Besoin d'aide ? <a href="#" class="text-blue-600 hover:underline">Contactez notre support</a></p>
        </div>
    </div>
</body>
</html>
