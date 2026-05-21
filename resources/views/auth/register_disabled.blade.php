<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription Désactivée - {{ config('app.name', 'Gestion Stock') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        .error-title {
            color: #dc3545;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-login {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,123,255,0.3);
            color: white;
            text-decoration: none;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">
            <i class="fas fa-warehouse"></i> {{ config('app.name', 'Gestion Stock') }}
        </div>
        
        <div class="error-icon">
            <i class="fas fa-user-lock"></i>
        </div>
        
        <h1 class="error-title">Inscription Désactivée</h1>
        
        <p class="error-message">
            L'inscription automatique est actuellement désactivée pour des raisons de sécurité.<br><br>
            Veuillez contacter votre administrateur système pour créer un nouveau compte.<br><br>
            Si vous avez déjà un compte, veuillez vous connecter.
        </p>
        
        <a href="{{ route('login') }}" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Se Connecter
        </a>
    </div>
</body>
</html>
