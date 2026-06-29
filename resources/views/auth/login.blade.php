<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.connexion') }} — {{ config('app.name', 'Asime') }}</title>
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
        .btn-shine:hover { box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4); }
    </style>
</head>
<body class="text-white flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-2">
                <x-application-logo class="w-8 h-8" />
                <span class="font-display font-bold text-xl text-white">{{ config('app.name', 'Asime') }}</span>
            </a>
        </div>

        <div class="glass-card rounded-2xl p-8">
            <h1 class="font-display text-2xl font-bold text-center mb-6">{{ __('messages.connexion') }}</h1>

            @if (session('status'))
                <div class="mb-4 bg-blue-500/10 border border-blue-500/30 text-blue-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-900/30 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">{{ __('messages.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-style w-full px-4 py-3 rounded-xl" placeholder="vous@exemple.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">{{ __('messages.mot_de_passe') }}</label>
                    <input id="password" type="password" name="password" required
                           class="input-style w-full px-4 py-3 rounded-xl" placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-gray-600 text-blue-500 focus:ring-blue-500 bg-gray-800">
                        <span class="text-sm text-gray-300">{{ __('messages.se_souvenir_de_moi') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:underline">
                            {{ __('messages.mot_de_passe_oublie') }} ?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-shine w-full py-3 px-4 rounded-xl text-white font-semibold">
                    {{ __('messages.se_connecter') }}
                </button>
            </form>

            <p class="text-center mt-6 text-gray-400 text-sm">
                Pas encore de compte ?
                <a href="{{ route('saas.register') }}" class="text-blue-400 hover:underline font-medium">Créer un compte gratuit</a>
            </p>
        </div>

        <p class="text-center mt-8 text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'Asime') }}. Tous droits réservés.
        </p>
    </div>
</body>
</html>
