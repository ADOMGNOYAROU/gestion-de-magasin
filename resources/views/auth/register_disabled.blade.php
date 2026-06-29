<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription — {{ config('app.name', 'Asime') }}</title>
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
    <div class="w-full max-w-md text-center glass-card rounded-2xl p-10">
        <div class="inline-flex mb-6">
            <x-application-logo class="w-16 h-16" />
        </div>

        <h1 class="font-display text-2xl font-bold mb-3">Créez votre compte</h1>
        <p class="text-gray-400 mb-8">
            L'inscription se fait désormais via notre formulaire d'essai gratuit
            ({{ config('plans.trial_days', 14) }} jours, sans carte bancaire).
        </p>

        <a href="{{ route('saas.register') }}" class="btn-shine block w-full py-3 px-4 rounded-xl text-white font-semibold mb-4">
            Créer mon compte gratuitement
        </a>

        <a href="{{ route('login') }}" class="block text-gray-400 hover:text-white text-sm transition-colors">
            <i class="fas fa-sign-in-alt mr-1"></i> J'ai déjà un compte, me connecter
        </a>
    </div>
</body>
</html>
