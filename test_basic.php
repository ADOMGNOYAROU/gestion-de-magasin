<?php

// Test if basic Laravel setup works
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel app loaded successfully\n";

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "✅ Laravel kernel bootstrapped successfully\n";

    // Test if User model works
    $user = App\Models\User::find(1);
    if ($user) {
        echo "✅ User model works: " . $user->name . "\n";
    } else {
        echo "⚠️ No user found\n";
    }

    // Test if Boutique model works
    $boutique = App\Models\Boutique::first();
    if ($boutique) {
        echo "✅ Boutique model works: " . $boutique->nom . "\n";
    } else {
        echo "⚠️ No boutique found\n";
    }

    echo "=== Basic Laravel test passed ===\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}
