# Guide d'Utilisation du Système de Permissions Laravel

## Vue d'Ensemble

Ce système utilise **Laravel Middleware** et **Gates** pour gérer les permissions à trois niveaux :
- **Middleware** : Protection au niveau des routes
- **Gates** : Vérifications granulaires dans les contrôleurs et vues
- **Helpers** : Fonctions utilitaires pour les vues Blade

## 1. Middleware Créés

### AdminMiddleware
- **Rôle autorisé** : `admin` uniquement
- **Usage** : Routes réservées aux administrateurs
- **Exemple** : `Route::get('/admin/users', 'AdminController@users')->middleware('admin')`

### GestionnaireMiddleware  
- **Rôles autorisés** : `admin`, `gestionnaire`
- **Usage** : Routes de gestion (produits, stock, transferts)
- **Exemple** : `Route::resource('produits', 'ProduitController')->middleware('gestionnaire')`

### VendeurMiddleware
- **Rôles autorisés** : `admin`, `gestionnaire`, `vendeur`
- **Usage** : Routes accessibles à tous les utilisateurs connectés
- **Exemple** : `Route::resource('ventes', 'VenteController')->middleware('vendeur')`

## 2. Gates Définis

### Gates de Gestion
```php
// Gérer les produits
Gate::define('manage-produits', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire();
});

// Gérer les entrées de stock
Gate::define('manage-entrees-stock', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire();
});

// Gérer les transferts
Gate::define('manage-transferts', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire();
});

// Gérer les ventes
Gate::define('manage-ventes', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire() || $user->isVendeur();
});

// Gérer les rapports
Gate::define('manage-rapports', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire();
});
```

### Gates Spécifiques aux Ressources
```php
// Gérer un magasin spécifique
Gate::define('manage-magasin', function (User $user, Magasin $magasin) {
    if ($user->isAdmin()) return true;
    return $user->isGestionnaire() && $user->magasinResponsable->id === $magasin->id;
});

// Gérer une boutique spécifique
Gate::define('manage-boutique', function (User $user, Boutique $boutique) {
    if ($user->isAdmin()) return true;
    if ($user->isGestionnaire() && $user->magasinResponsable->id === $boutique->magasin_id) return true;
    return $user->isVendeur() && $user->boutique->id === $boutique->id;
});

// Vendre dans une boutique
Gate::define('sell-in-boutique', function (User $user, Boutique $boutique) {
    if ($user->isAdmin()) return true;
    if ($user->isGestionnaire() && $user->magasinResponsable->id === $boutique->magasin_id) return true;
    return $user->isVendeur() && $user->boutique->id === $boutique->id;
});
```

### Gates de Visualisation
```php
// Voir les statistiques globales (admin uniquement)
Gate::define('view-global-stats', function (User $user) {
    return $user->isAdmin();
});

// Voir les rapports partenaires
Gate::define('view-rapports-partenaires', function (User $user) {
    return $user->isAdmin() || $user->isGestionnaire();
});

// Voir le stock d'un magasin
Gate::define('view-stock-magasin', function (User $user, Magasin $magasin) {
    if ($user->isAdmin()) return true;
    return $user->isGestionnaire() && $user->magasinResponsable->id === $magasin->id;
});
```

## 3. Protection des Routes

### Configuration dans `routes/web.php`
```php
// Routes protégées par middleware
Route::resource('produits', ProduitController::class)->middleware('gestionnaire');
Route::resource('entrees-stock', EntreeStockController::class)->middleware('gestionnaire');
Route::resource('transferts', TransfertController::class)->middleware('gestionnaire');
Route::resource('ventes', VenteController::class)->middleware('vendeur');

// Routes de rapports
Route::get('/rapports', [RapportController::class, 'index'])->middleware('gestionnaire');
Route::get('/rapports/stock/pdf', [RapportController::class, 'rapportStockPDF'])->middleware('gestionnaire');

// Routes API
Route::get('/api/stock-disponible', [TransfertController::class, 'getStockDisponible'])->middleware('gestionnaire');
Route::post('/api/panier/ajouter', [VenteController::class, 'ajouterPanier'])->middleware('vendeur');
```

## 4. Utilisation dans les Contrôleurs

### Vérification avec Gates
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        // Vérification simple
        Gate::authorize('manage-produits');
        
        // Logique du contrôleur
        $produits = Produit::paginate(10);
        return view('produits.index', compact('produits'));
    }
    
    public function edit(Produit $produit)
    {
        // Vérification avec ressource
        Gate::authorize('manage-produits');
        
        return view('produits.edit', compact('produit'));
    }
}
```

### Vérification conditionnelle
```php
public function destroy(Transfert $transfert)
{
    // Vérifier si l'utilisateur peut gérer ce transfert
    if (!Gate::allows('manage-transferts')) {
        abort(403, 'Accès non autorisé');
    }
    
    // Logique de suppression
    $transfert->delete();
    
    return redirect()->route('transferts.index')
        ->with('success', 'Transfert supprimé avec succès');
}
```

### Vérification avec ressource spécifique
```php
public function update(Request $request, Boutique $boutique)
{
    // Vérifier si l'utilisateur peut gérer cette boutique
    Gate::authorize('manage-boutique', $boutique);
    
    // Logique de mise à jour
    $boutique->update($request->validated());
    
    return redirect()->route('boutiques.show', $boutique)
        ->with('success', 'Boutique mise à jour');
}
```

## 5. Utilisation dans les Vues Blade

### Helpers de Permissions
```php
<!-- Cacher un élément si pas la permission -->
<a href="{{ route('rapports.index') }}" class="btn btn-primary {{ hideIfCannot('manage-rapports') }}">
    <i class="fas fa-file-alt"></i> Rapports
</a>

<!-- Désactiver un bouton si pas la permission -->
<button class="btn btn-danger {{ disabledIfCannot('manage-produits') }}" onclick="deleteProduct()">
    Supprimer
</button>

<!-- Vérification conditionnelle -->
@if (canManageRapports())
    <div class="card">
        <h5>Rapports disponibles</h5>
        <!-- Contenu des rapports -->
    </div>
@endif

<!-- Vérification avec ressource -->
@if (canManageBoutique($boutique))
    <a href="{{ route('boutiques.edit', $boutique) }}" class="btn btn-sm btn-outline-primary">
        Modifier
    </a>
@endif
```

### Exemple complet dans une vue
```php
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Produits</h1>
        <div>
            <!-- Bouton visible seulement si peut gérer les rapports -->
            <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-outline-danger me-2 {{ hideIfCannot('manage-rapports') }}">
                <i class="fas fa-warehouse"></i> Rapport Stock
            </a>
            
            <!-- Bouton visible pour tout le monde -->
            <a href="{{ route('produits.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Produit
            </a>
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $produit)
                    <tr>
                        <td>{{ $produit->nom }}</td>
                        <td>{{ $produit->categorie }}</td>
                        <td>{{ $produit->prix_vente }} FCFA</td>
                        <td>
                            <!-- Actions selon permissions -->
                            <a href="{{ route('produits.show', $produit) }}" class="btn btn-sm btn-info">
                                Voir
                            </a>
                            
                            @if (canManageProduits())
                                <a href="{{ route('produits.edit', $produit) }}" class="btn btn-sm btn-warning">
                                    Modifier
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

## 6. Helpers Disponibles

### Helpers de Vérification
```php
// Vérifications simples
canManageProduits()
canManageEntreesStock()
canManageTransferts()
canManageVentes()
canManageRapports()
canViewRapportsPartenaires()
canViewGlobalStats()

// Vérifications avec ressource
canManageMagasin($magasin)
canManageBoutique($boutique)
canTransferFromMagasin($magasin)
canSellInBoutique($boutique)
canViewStockMagasin($magasin)
canViewStockBoutique($boutique)
```

### Helpers de Style
```php
// Cacher un élément
hideIfCannot('permission', $resource = null)

// Désactiver un élément
disabledIfCannot('permission', $resource = null)
```

## 7. Bonnes Pratiques

### Dans les Contrôleurs
1. **Toujours vérifier les permissions** au début des méthodes sensibles
2. **Utiliser `Gate::authorize()`** pour les vérifications obligatoires (403 automatique)
3. **Utiliser `Gate::allows()`** pour les vérifications conditionnelles
4. **Combiner middleware et gates** pour une sécurité en profondeur

### Dans les Vues
1. **Utiliser les helpers** plutôt que les vérifications manuelles
2. **Cacher plutôt que désactiver** pour une meilleure UX
3. **Gérer les cas limites** (utilisateur non connecté)
4. **Être cohérent** avec les permissions du contrôleur

### Architecture
1. **Middleware** : Protection première ligne (routes)
2. **Gates** : Vérifications granulaires (contrôleurs)
3. **Helpers** : UI conditionnelle (vues)
4. **Logging** : Suivi des tentatives d'accès non autorisées

## 8. Exemples d'Utilisation Avancés

### Vérification multiple
```php
// Dans un contrôleur
public function bulkDelete(Request $request)
{
    Gate::authorize('manage-produits');
    
    $produits = Produit::whereIn('id', $request->produits)->get();
    
    foreach ($produits as $produit) {
        // Vérification supplémentaire si nécessaire
        $this->authorize('delete', $produit);
        $produit->delete();
    }
    
    return back()->with('success', 'Produits supprimés');
}
```

### Permissions dynamiques
```php
// Dans une vue
@foreach($magasins as $magasin)
    <div class="card">
        <h5>{{ $magasin->nom }}</h5>
        
        @if (canManageMagasin($magasin))
            <a href="{{ route('magasins.edit', $magasin) }}" class="btn btn-sm btn-primary">
                Gérer
            </a>
        @endif
        
        @if (canTransferFromMagasin($magasin))
            <a href="{{ route('transferts.create', ['magasin_id' => $magasin->id]) }}" class="btn btn-sm btn-success">
                Transférer
            </a>
        @endif
    </div>
@endforeach
```

### Messages d'erreur personnalisés
```php
// Dans un contrôleur
public function destroy(Vente $vente)
{
    try {
        Gate::authorize('delete', $vente);
        $vente->delete();
        return redirect()->route('ventes.index')
            ->with('success', 'Vente supprimée avec succès');
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return back()->with('error', 'Vous ne pouvez pas supprimer cette vente.');
    }
}
```

Ce système de permissions offre une **sécurité robuste**, une **granularité fine** et une **facilité d'utilisation** pour les développeurs.
