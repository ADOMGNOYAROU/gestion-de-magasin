<?php

// Very simple test - just try to load the route
echo "=== Simple POS Route Test ===\n";

try {
    // Test if we can load Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "✅ Laravel loaded\n";

    // Try to create a simple request to /pos
    $request = Illuminate\Http\Request::create('/pos', 'GET');
    echo "✅ Request created\n";

    // Try to find the route
    $routes = Illuminate\Support\Facades\Route::getRoutes();
    $posRoute = null;
    foreach ($routes as $route) {
        if ($route->getName() === 'pos.index') {
            $posRoute = $route;
            break;
        }
    }

    if ($posRoute) {
        echo "✅ Route pos.index found\n";
        echo "URI: " . $posRoute->uri() . "\n";
        echo "Methods: " . implode(', ', $posRoute->methods()) . "\n";
    } else {
        echo "❌ Route pos.index not found\n";
        foreach ($routes as $route) {
            if (strpos($route->getName(), 'pos.') === 0) {
                echo "   Found: " . $route->getName() . " -> " . $route->uri() . "\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " line " . $e->getLine() . "\n";
}

echo "=== Test End ===\n";
