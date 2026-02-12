<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug POS Route Access ===\n";

try {
    // Test simple de la route
    $routes = \Illuminate\Support\Facades\Route::getRoutes();

    echo "Routes trouvées: " . count($routes) . "\n";

    $posRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->getName(), 'pos.') === 0) {
            $posRoutes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'middleware' => $route->middleware()
            ];
        }
    }

    echo "\n--- Routes POS ---\n";
    foreach ($posRoutes as $route) {
        echo $route['name'] . " -> " . implode(',', $route['methods']) . " " . $route['uri'];
        echo " (middleware: " . implode(', ', $route['middleware']) . ")\n";
    }

    // Test de l'utilisateur admin
    $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
    if (!$admin) {
        echo "\n❌ Admin non trouvé\n";
        exit;
    }

    echo "\n✅ Admin trouvé: " . $admin->name . "\n";

    // Simuler la requête HTTP
    echo "\n--- Simulation requête HTTP ---\n";
    $request = \Illuminate\Http\Request::create('/pos', 'GET');

    // Authentifier l'admin
    \Illuminate\Support\Facades\Auth::login($admin);

    echo "✅ Admin authentifié\n";

    // Essayer de dispatcher la route
    try {
        $response = \Illuminate\Support\Facades\Route::dispatch($request);
        echo "✅ Route dispatchée avec succès\n";
        echo "Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() === 200) {
            echo "✅ Réponse 200 - Route accessible\n";
        } else {
            echo "⚠️ Status " . $response->getStatusCode() . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erreur lors du dispatch: " . $e->getMessage() . "\n";
        echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    }

} catch (\Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}

echo "\n=== Fin Debug ===\n";
