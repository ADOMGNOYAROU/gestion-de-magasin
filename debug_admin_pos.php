<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug POS Admin Section ===\n";

try {
    // Simuler l'authentification admin
    $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
    if (!$admin) {
        echo "❌ Admin non trouvé\n";
        exit;
    }

    echo "✅ Admin trouvé: " . $admin->name . "\n";
    echo "   Role: " . $admin->role . "\n";
    echo "   isAdmin(): " . ($admin->isAdmin() ? 'true' : 'false') . "\n";

    // Authentifier l'admin
    \Illuminate\Support\Facades\Auth::login($admin);
    $user = \Illuminate\Support\Facades\Auth::user();

    echo "\n--- Test logique admin POSController ---\n";

    // Simuler la logique du POSController pour admin
    if ($user->isAdmin()) {
        echo "✅ Utilisateur est admin\n";

        // Test récupération boutiques
        echo "Test boutiques...\n";
        $boutiques = \App\Models\Boutique::with(['magasin'])->get();
        echo "✅ Boutiques trouvées: " . $boutiques->count() . "\n";

        foreach ($boutiques as $boutique) {
            echo "   - " . $boutique->nom . " (Magasin: " . ($boutique->magasin ? $boutique->magasin->nom : 'null') . ")\n";
        }

        // Test sessions actives
        echo "\nTest sessions actives...\n";
        $sessionsActives = \App\Models\CashRegisterSession::with(['vendeur', 'boutique'])
                                            ->whereIn('status', ['ouverte', 'en_cours'])
                                            ->get();
        echo "✅ Sessions actives trouvées: " . $sessionsActives->count() . "\n";

        // Test rendu de vue
        echo "\nTest rendu de vue...\n";
        $view = view('pos.admin', compact('boutiques', 'sessionsActives'));
        echo "✅ Vue rendue avec succès\n";

    } else {
        echo "❌ Utilisateur n'est pas admin\n";
    }

} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Fin Debug ===\n";
