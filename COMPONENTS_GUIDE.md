# Guide des Composants UI

Ce guide présente tous les composants réutilisables créés pour l'application de gestion de stock.

## 📋 Table des Matières

1. [Composants de Formulaire](#composants-de-formulaire)
2. [Composants d'Affichage](#composants-daffichage)
3. [Composants de Navigation](#composants-de-navigation)
4. [Composants d'Interaction](#composants-dinteraction)
5. [Exemples d'Utilisation](#examples-dutilisation)

---

## 🎯 Composants de Formulaire

### Input Texte
```blade
<x-input 
    label="Nom du produit" 
    name="nom" 
    placeholder="Entrez le nom du produit"
    required
    icon="fas fa-box"
    help="Le nom doit être unique"
    error="{{ $errors->get('nom') }}"
    value="{{ old('nom') }}" />
```

### Select
```blade
<x-select 
    label="Catégorie" 
    name="categorie_id"
    required
    icon="fas fa-tag"
    placeholder="Sélectionnez une catégorie">
    <option value="1">Électronique</option>
    <option value="2">Vêtements</option>
</x-select>
```

### Textarea
```blade
<x-textarea 
    label="Description" 
    name="description"
    rows="4"
    placeholder="Entrez la description"
    value="{{ old('description') }}" />
```

### Radio Buttons
```blade
<x-radio 
    label="Statut" 
    name="statut"
    :options="['actif' => 'Actif', 'inactif' => 'Inactif']"
    value="{{ old('statut', 'actif') }}" />
```

### Checkbox
```blade
<x-checkbox 
    label="Accepter les conditions" 
    name="conditions"
    required
    switch />
```

---

## 📊 Composants d'Affichage

### Card
```blade
<x-card title="Produits" color="primary" :collapsible="true">
    <p>Contenu de la carte...</p>
    
    <x-slot name="actions">
        <button class="btn btn-sm btn-primary">Ajouter</button>
    </x-slot>
</x-card>
```

### Data Table
```blade
<x-data-table 
    :headers="[
        ['title' => 'Nom', 'key' => 'nom'],
        ['title' => 'Catégorie', 'key' => 'categorie'],
        ['title' => 'Prix', 'key' => 'prix', 'class' => 'text-end']
    ]"
    :rows="$produits"
    :actions="[
        ['type' => 'link', 'url' => route('produits.show', $row), 'icon' => 'fas fa-eye'],
        ['type' => 'link', 'url' => route('produits.edit', $row), 'icon' => 'fas fa-edit'],
        ['type' => 'form', 'method' => 'DELETE', 'url' => route('produits.destroy', $row), 'icon' => 'fas fa-trash']
    ]" />
```

### Stat Card
```blade
<x-stat-card 
    title="Total Produits" 
    value="{{ $totalProduits }}"
    icon="fas fa-box"
    color="primary"
    :trend="'up'"
    trend-value="+12%" />
```

### Progress Bar
```blade
<x-progress-bar 
    label="Stock utilisé"
    :value="$stockUtilise"
    :max="$stockTotal"
    color="success"
    :striped="true" />
```

---

## 🔍 Composants de Navigation

### Breadcrumb
```blade
<x-breadcrumb 
    :items="[
        ['title' => 'Produits', 'url' => route('produits.index')],
        ['title' => $produit->nom]
    ]" />
```

### Search Bar
```blade
<x-search-bar 
    placeholder="Rechercher un produit..."
    method="GET" />
```

### Filter Form
```blade
<x-filter-form>
    <x-slot>
        <div class="col-md-4">
            <x-select name="categorie" label="Catégorie">
                <option value="">Toutes</option>
                <option value="electronique">Électronique</option>
            </x-select>
        </div>
        <div class="col-md-4">
            <x-input name="prix_min" type="number" label="Prix min" placeholder="0" />
        </div>
        <div class="col-md-4">
            <x-input name="prix_max" type="number" label="Prix max" placeholder="999999" />
        </div>
    </x-slot>
</x-filter-form>
```

### Pagination
```blade
<x-pagination :data="$produits" />
```

---

## ⚡ Composants d'Interaction

### Alert
```blade
<x-alert type="success" :dismissible="false">
    Opération réussie !
</x-alert>

<x-alert type="warning" icon>
    Attention : Cette action est irréversible.
</x-alert>
```

### Confirm Modal
```blade
@include('components.confirm-modal')

<script>
// Utilisation
showConfirmModal({
    title: 'Supprimer le produit',
    message: 'Êtes-vous sûr de vouloir supprimer ce produit ?',
    confirmText: 'Supprimer',
    confirmClass: 'btn-danger',
    onConfirm: function() {
        // Action de confirmation
        window.location.href = deleteUrl;
    }
});
</script>
```

### Loading
```blade
<x-loading text="Chargement des produits..." :overlay="true" />
```

### Button Group
```blade
<x-button-group>
    <button class="btn btn-primary">Action 1</button>
    <button class="btn btn-secondary">Action 2</button>
    <button class="btn btn-danger">Action 3</button>
</x-button-group>
```

---

## 🎨 Exemples d'Utilisation

### Formulaire Complet
```blade
<x-card title="Ajouter un produit" color="primary">
    <form method="POST" action="{{ route('produits.store') }}">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <x-input 
                    label="Nom du produit" 
                    name="nom" 
                    required
                    icon="fas fa-box"
                    :error="$errors->get('nom')" />
            </div>
            
            <div class="col-md-6">
                <x-select 
                    label="Catégorie" 
                    name="categorie_id"
                    required
                    icon="fas fa-tag">
                    <option value="">Sélectionnez...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nom }}</option>
                    @endforeach
                </x-select>
            </div>
            
            <div class="col-md-4">
                <x-input 
                    label="Prix de vente" 
                    name="prix_vente" 
                    type="number"
                    step="0.01"
                    required
                    icon="fas fa-euro-sign" />
            </div>
            
            <div class="col-md-4">
                <x-input 
                    label="Seuil d'alerte" 
                    name="seuil_alerte" 
                    type="number"
                    icon="fas fa-exclamation-triangle" />
            </div>
            
            <div class="col-md-4">
                <x-select 
                    label="Statut" 
                    name="statut"
                    required>
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </x-select>
            </div>
            
            <div class="col-12">
                <x-textarea 
                    label="Description" 
                    name="description"
                    rows="3" />
            </div>
            
            <div class="col-12">
                <x-checkbox 
                    label="Notifier quand le stock est bas" 
                    name="notification_stock"
                    switch />
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('produits.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Enregistrer
            </button>
        </div>
    </form>
</x-card>
```

### Page avec Tableau et Filtres
```blade
@section('header')
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-box"></i> Gestion des Produits
    </h1>
    <div>
        <a href="{{ route('produits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Produit
        </a>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Produits</li>
@endsection

<x-filter-form>
    <x-slot>
        <div class="col-md-3">
            <x-input name="search" placeholder="Rechercher..." value="{{ request('search') }}" />
        </div>
        <div class="col-md-3">
            <x-select name="categorie">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('categorie') == $category->id ? 'selected' : '' }}>
                        {{ $category->nom }}
                    </option>
                @endforeach
            </x-select>
        </div>
        <div class="col-md-3">
            <x-select name="statut">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
            </x-select>
        </div>
    </x-slot>
</x-filter-form>

<x-data-table 
    :headers="[
        ['title' => 'Nom', 'key' => 'nom'],
        ['title' => 'Catégorie', 'key' => 'categorie'],
        ['title' => 'Prix', 'key' => 'prix_vente', 'class' => 'text-end'],
        ['title' => 'Stock', 'key' => 'stock_total', 'class' => 'text-center'],
        ['title' => 'Statut', 'key' => 'statut', 'class' => 'text-center']
    ]"
    :rows="$produits"
    :actions="[
        ['type' => 'link', 'url' => route('produits.show', $row), 'icon' => 'fas fa-eye', 'title' => 'Voir'],
        ['type' => 'link', 'url' => route('produits.edit', $row), 'icon' => 'fas fa-edit', 'title' => 'Modifier'],
        ['type' => 'form', 'method' => 'DELETE', 'url' => route('produits.destroy', $row), 'icon' => 'fas fa-trash', 'title' => 'Supprimer', 'class' => 'btn-outline-danger']
    ]" />

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        <small>{{ $produits->count() }} produit(s) trouvé(s)</small>
    </div>
    <x-pagination :data="$produits" />
</div>
```

### Dashboard avec Statistiques
```blade
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <x-stat-card 
            title="Total Produits" 
            value="{{ $stats->totalProduits }}"
            icon="fas fa-box"
            color="primary"
            :trend="'up'"
            trend-value="+5%" />
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <x-stat-card 
            title="Ventes du Jour" 
            value="{{ $stats->ventesJour }}"
            icon="fas fa-shopping-cart"
            color="success"
            :trend="'up'"
            trend-value="+12%" />
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <x-stat-card 
            title="Chiffre d'Affaires" 
            value="{{ number_format($stats->ca, 0, ',', ' ') }} FCFA"
            icon="fas fa-euro-sign"
            color="info"
            :trend="'down'"
            trend-value="-3%" />
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <x-stat-card 
            title="Stock Critique" 
            value="{{ $stats->stockCritique }}"
            icon="fas fa-exclamation-triangle"
            color="warning" />
    </div>
</div>

<x-card title="État du Stock" :collapsible="true">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Stock Actuel</th>
                    <th>Seuil Alerte</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produitsCritiques as $produit)
                    <tr>
                        <td>{{ $produit->nom }}</td>
                        <td class="text-center">{{ $produit->stock }}</td>
                        <td class="text-center">{{ $produit->seuil_alerte }}</td>
                        <td class="text-center">
                            <span class="badge bg-danger">Critique</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-card>
```

---

## 🎯 Bonnes Pratiques

1. **Utiliser les composants** : Privilégiez toujours les composants réutilisables
2. **Validation côté serveur** : Complétez toujours avec une validation Laravel
3. **Accessibilité** : Utilisez les attributs ARIA et les labels appropriés
4. **Responsive** : Testez l'affichage sur mobile et desktop
5. **Performance** : Limitez le nombre de composants par page

---

## 📝 Notes Techniques

- Tous les composants utilisent Bootstrap 5
- Les icônes proviennent de Font Awesome 6
- Les formulaires sont compatibles avec la validation Laravel
- Les DataTables sont configurés en français
- Le design est responsive et moderne

Pour plus d'exemples, consultez les fichiers de vues existants dans l'application.
