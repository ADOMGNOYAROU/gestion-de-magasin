<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Final Test POS Admin Access ===\n";

try {
    // Test de l'utilisateur admin
    $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
    if (!$admin) {
        echo "❌ Admin non trouvé\n";
        exit;
    }

    echo "✅ Admin trouvé: " . $admin->name . "\n";

    // Authentifier l'admin
    \Illuminate\Support\Facades\Auth::login($admin);

    // Simuler la logique du POSController
    $user = \Illuminate\Support\Facades\Auth::user();

    echo "✅ Admin authentifié\n";
    echo "Role: " . $user->role . "\n";
    echo "isAdmin(): " . ($user->isAdmin() ? 'true' : 'false') . "\n";

    // Test de la logique admin
    if ($user->isAdmin()) {
        echo "\n--- Test récupération boutiques ---\n";

        $boutiques = \App\Models\Boutique::with(['magasin'])->get();
        echo "✅ Boutiques trouvées: " . $boutiques->count() . "\n";

        foreach ($boutiques as $boutique) {
            $magasinNom = $boutique->magasin ? $boutique->magasin->nom : 'Aucun magasin';
            echo "   - " . $boutique->nom . " (Magasin: " . $magasinNom . ")\n";
        }

        echo "\n--- Test sessions actives ---\n";
        $sessionsActives = \App\Models\CashRegisterSession::with(['vendeur', 'boutique'])
                                            ->whereIn('status', ['ouverte', 'en_cours'])
                                            ->get();
        echo "✅ Sessions actives: " . $sessionsActives->count() . "\n";

        echo "\n--- Test rendu de vue ---\n";
        try {
            $view = view('pos.admin', compact('boutiques', 'sessionsActives'));
            echo "✅ Vue 'pos.admin' rendue avec succès\n";
        } catch (\Exception $e) {
            echo "❌ Erreur rendu vue: " . $e->getMessage() . "\n";
            echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
        }

    } else {
        echo "❌ Utilisateur n'est pas admin\n";
    }

} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test terminé ===\n";
