# Cahier des Charges - Gestion de Magasin

## 1. Présentation du Projet

### 1.1 Contexte
Le projet **Gestion de Magasin** est une application web complète de gestion de stock et de vente, développée avec le framework Laravel 12. Cette application permet de gérer efficacement les opérations d'un réseau de magasins et boutiques, depuis l'approvisionnement jusqu'à la vente en passant par la gestion des stocks.

### 1.2 Objectifs
- Centraliser la gestion des stocks sur plusieurs sites (magasins et boutiques)
- Automatiser les processus d'entrée de stock et de transfert
- Faciliter les opérations de vente via un système de caisse (POS)
- Générer des rapports détaillés pour la prise de décision
- Gérer les utilisateurs avec des droits d'accès différenciés

### 1.3 Portée du Projet
L'application couvre :
- La gestion multi-sites (magasins et boutiques)
- La gestion des produits et catégories
- La gestion des stocks en temps réel
- Les opérations d'achat (fournisseurs) et de vente
- Les transferts de stock entre sites
- La génération de rapports statistiques
- La gestion des utilisateurs et permissions

---

## 2. Architecture Technique

### 2.1 Stack Technologique

#### Backend
- **Framework** : Laravel 12 (PHP 8.2+)
- **Base de données** : MySQL/SQLite
- **ORM** : Eloquent ORM
- **Authentification** : Laravel Breeze
- **Gestion des permissions** : Middleware + Gates

#### Frontend
- **Framework CSS** : Bootstrap 5
- **Framework JS** : Alpine.js
- **Build Tool** : Vite
- **Icônes** : Font Awesome 6
- **Styling** : TailwindCSS

#### Packages Principaux
- **barryvdh/laravel-dompdf** : Génération de PDF
- **maatwebsite/excel** : Export Excel
- **laravel/breeze** : Authentification

### 2.2 Structure de l'Application

```
app/
├── Http/
│   ├── Controllers/       # Contrôleurs MVC
│   ├── Middleware/        # Middleware de permissions
│   └── Requests/          # Validation des formulaires
├── Models/               # Modèles Eloquent
├── Helpers/              # Fonctions utilitaires
└── Exports/              # Classes d'export Excel

database/
├── migrations/           # Migrations de base de données
├── seeders/              # Données de test
└── factories/            # Factories pour les tests

resources/
├── views/                # Templates Blade
│   ├── components/       # Composants réutilisables
│   ├── layouts/          # Mises en page
│   └── pos/              # Vues du système POS
└── js/                   # Scripts JavaScript

routes/
├── web.php               # Routes web
└── api.php               # Routes API
```

---

## 3. Modèle de Données

### 3.1 Entités Principales

#### Users (Utilisateurs)
- **Champs** : id, name, email, password, role, magasin_id, boutique_id
- **Rôles** : admin, gestionnaire, vendeur
- **Relations** : magasinResponsable, boutique, magasinsGeres

#### Magasins
- **Champs** : id, nom, adresse, telephone, email, localisation, responsable_id
- **Relations** : responsable, boutiques, stockMagasins

#### Boutiques
- **Champs** : id, nom, adresse, telephone, email, magasin_id, vendeur_id
- **Relations** : magasin, vendeur, stockBoutiques

#### Produits
- **Champs** : id, nom, categorie, description, prix_achat, prix_vente, statut, seuil_alerte
- **Relations** : stockMagasins, stockBoutiques, entreesStock, ventes

#### StockMagasins
- **Champs** : id, produit_id, magasin_id, quantite, seuil_alerte
- **Relations** : produit, magasin

#### StockBoutiques
- **Champs** : id, produit_id, boutique_id, quantite, seuil_alerte
- **Relations** : produit, boutique

#### EntreeStock
- **Champs** : id, produit_id, magasin_id, fournisseur_id, partenaire_id, quantite, prix_unitaire, date_entree
- **Relations** : produit, magasin, fournisseur, partenaire

#### Transfert
- **Champs** : id, produit_id, magasin_id, boutique_id, quantite, date_transfert, statut
- **Relations** : produit, magasin, boutique

#### Vente
- **Champs** : id, boutique_id, user_id, session_caisse_id, payment_method_id, montant_total, montant_recu, monnaie, numero_ticket, status, date_vente
- **Relations** : boutique, user, sessionCaisse, paymentMethod, venteProduits

#### VenteProduit
- **Champs** : id, vente_id, produit_id, quantite, prix_unitaire, remise, sous_total
- **Relations** : vente, produit

#### CashRegisterSession
- **Champs** : id, boutique_id, user_id, montant_ouverture, montant_fermeture, montant_theorique, date_ouverture, date_fermeture, statut
- **Relations** : boutique, user, ventes

#### Fournisseurs
- **Champs** : id, nom, adresse, telephone, email
- **Relations** : entreesStock

#### Partenaires
- **Champs** : id, nom, adresse, telephone, email
- **Relations** : entreesStock

#### PaymentMethod
- **Champs** : id, nom, description
- **Relations** : ventes

### 3.2 Diagramme des Relations

```
User (1) ----< (1) Magasin (1) ----< (*) Boutique
  |                   |                   |
  |                   |                   |
  +-- (responsable)  +-- (stock)         +-- (stock)
                      |                   |
                      |                   |
                  StockMagasin         StockBoutique
                      |                   |
                      |                   |
                      +----> Produit <----+
                              |
                              |
                    +---------+---------+
                    |                   |
              EntreeStock          VenteProduit
                    |                   |
                    |                   |
              Fournisseur             Vente
                    |                   |
                    |                   |
              Partenaire         CashRegisterSession
                                        |
                                        |
                                  PaymentMethod
```

---

## 4. Fonctionnalités

### 4.1 Gestion des Utilisateurs

#### 4.1.1 Authentification
- Inscription avec validation des données
- Connexion sécurisée avec sessions
- Réinitialisation de mot de passe
- Déconnexion

#### 4.1.2 Rôles et Permissions
- **Admin** : Accès total à toutes les fonctionnalités
- **Gestionnaire** : Gestion des produits, stocks, transferts, rapports
- **Vendeur** : Opérations de vente uniquement

#### 4.1.3 Gestion des Profils
- Modification des informations personnelles
- Changement de mot de passe
- Suppression de compte

### 4.2 Gestion des Produits

#### 4.2.1 CRUD Produits
- Création de produits avec validation
- Liste des produits avec pagination et recherche
- Modification des informations produit
- Suppression (soft delete) des produits
- Réactivation des produits supprimés

#### 4.2.2 Informations Produit
- Nom et description
- Catégorie
- Prix d'achat et prix de vente
- Statut (actif/inactif)
- Seuil d'alerte de stock
- Calcul automatique de la marge

#### 4.2.3 Recherche et Filtrage
- Recherche par nom
- Filtrage par catégorie
- Filtrage par statut
- Tri par prix ou stock

### 4.3 Gestion des Magasins

#### 4.3.1 CRUD Magasins
- Création de magasins
- Liste des magasins
- Modification des informations
- Suppression de magasins

#### 4.3.2 Informations Magasin
- Nom et adresse
- Téléphone et email
- Localisation
- Responsable (gestionnaire assigné)

#### 4.3.3 Gestion des Boutiques
- Création de boutiques rattachées à un magasin
- Assignation d'un vendeur
- Suivi du stock par boutique

### 4.4 Gestion des Stocks

#### 4.4.1 Stock Magasin
- Visualisation du stock par magasin
- Mise à jour automatique lors des entrées
- Alertes de stock bas
- Historique des mouvements

#### 4.4.2 Stock Boutique
- Visualisation du stock par boutique
- Mise à jour automatique lors des ventes
- Alertes de stock bas
- Historique des mouvements

#### 4.4.3 Entrées de Stock
- Enregistrement des entrées de stock
- Sélection du fournisseur ou partenaire
- Mise à jour automatique du stock
- Génération de reçus

#### 4.4.4 Transferts
- Transfert de stock entre magasin et boutique
- Vérification de la disponibilité
- Suivi du statut du transfert
- Historique des transferts

### 4.5 Système de Caisse (POS)

#### 4.5.1 Gestion des Sessions de Caisse
- Ouverture de session avec montant initial
- Enregistrement des ventes
- Fermeture de session avec vérification
- Calcul automatique du montant théorique
- Détection des écarts

#### 4.5.2 Processus de Vente
- Recherche de produits (nom, référence)
- Ajout au panier
- Modification des quantités
- Application de remises
- Sélection du mode de paiement
- Calcul de la monnaie
- Génération du ticket de caisse

#### 4.5.3 Modes de Paiement
- Espèces
- Carte bancaire
- Mobile Money
- Chèque
- Autres

#### 4.5.4 Gestion du Panier
- Ajout de produits
- Modification des quantités
- Suppression d'articles
- Vidage du panier
- Calcul en temps réel du total

### 4.6 Rapports

#### 4.6.1 Rapport de Stock
- État du stock global
- État du stock par magasin
- État du stock par boutique
- Produits en stock critique
- Export en PDF

#### 4.6.2 Rapport de Ventes
- Ventes par période
- Ventes par boutique
- Ventes par vendeur
- Chiffre d'affaires
- Produits les plus vendus
- Export en PDF et Excel

#### 4.6.3 Rapport Partenaires
- Volume d'affaires par partenaire
- État des partenariats
- Export en PDF

#### 4.6.4 Dashboard
- Statistiques en temps réel
- Graphiques de ventes
- Alertes de stock
- Dernières opérations

### 4.7 Gestion des Fournisseurs

#### 4.7.1 CRUD Fournisseurs
- Création de fournisseurs
- Liste des fournisseurs
- Modification des informations
- Suppression de fournisseurs

#### 4.7.2 Informations Fournisseur
- Nom de l'entreprise
- Adresse et contact
- Historique des approvisionnements

### 4.8 Gestion des Partenaires

#### 4.8.1 CRUD Partenaires
- Création de partenaires
- Liste des partenaires
- Modification des informations
- Suppression de partenaires

#### 4.8.2 Informations Partenaire
- Nom du partenaire
- Adresse et contact
- Historique des transactions

---

## 5. Interface Utilisateur

### 5.1 Design System

#### 5.1.1 Composants UI
L'application utilise une bibliothèque de composants réutilisables :
- **Formulaires** : Input, Select, Textarea, Radio, Checkbox
- **Affichage** : Card, DataTable, StatCard, ProgressBar
- **Navigation** : Breadcrumb, SearchBar, FilterForm, Pagination
- **Interaction** : Alert, ConfirmModal, Loading, ButtonGroup

#### 5.1.2 Palette de Couleurs
- **Primary** : Bleu (#4e73df)
- **Success** : Vert (#1cc88a)
- **Info** : Cyan (#36b9cc)
- **Warning** : Jaune (#f6c23e)
- **Danger** : Rouge (#e74a3b)

#### 5.1.3 Responsive Design
- Adaptation mobile, tablette et desktop
- Navigation latérale rétractable
- Tableaux responsive
- Formulaires adaptatifs

### 5.2 Pages Principales

#### 5.2.1 Dashboard
- Cartes de statistiques (produits, ventes, CA, stock critique)
- Graphiques de tendance
- Liste des alertes
- Dernières opérations

#### 5.2.2 Liste des Produits
- Tableau avec pagination
- Barre de recherche
- Filtres (catégorie, statut)
- Actions (voir, modifier, supprimer)
- Bouton d'ajout

#### 5.2.3 Détail Produit
- Informations complètes
- Stock par magasin
- Stock par boutique
- Historique des mouvements
- Boutons d'action

#### 5.2.4 POS (Point of Sale)
- Interface de caisse intuitive
- Recherche de produits
- Panier en temps réel
- Modes de paiement
- Génération de ticket

#### 5.2.5 Rapports
- Sélection de période
- Choix de format (PDF/Excel)
- Prévisualisation
- Téléchargement

---

## 6. Sécurité

### 6.1 Authentification
- Hachage des mots de passe (bcrypt)
- Protection contre les attaques CSRF
- Sessions sécurisées
- Rate limiting sur les tentatives de connexion

### 6.2 Autorisations
- Middleware de protection des routes
- Gates pour les vérifications granulaires
- Helpers pour les vues Blade
- Séparation des rôles

### 6.3 Validation des Données
- Validation côté serveur (Laravel)
- Validation côté client (JavaScript)
- Protection contre l'injection SQL (Eloquent)
- Sanitization des entrées

### 6.4 Logs et Audit
- Journalisation des actions sensibles
- Suivi des connexions
- Historique des modifications
- Alertes en cas d'anomalie

---

## 7. Performance

### 7.1 Optimisation
- Pagination des listes
- Indexation de la base de données
- Mise en cache des requêtes fréquentes
- Lazy loading des relations

### 7.2 Scalabilité
- Architecture modulaire
- Séparation des responsabilités
- Code réutilisable
- Documentation complète

---

## 8. Déploiement

### 8.1 Environnement de Développement
- PHP 8.2+
- Composer
- Node.js et NPM
- MySQL ou SQLite
- Serveur web (Apache/Nginx)

### 8.2 Configuration
- Fichier `.env` pour les variables d'environnement
- Configuration de la base de données
- Clé d'application Laravel
- Configuration des assets

### 8.3 Installation
```bash
# Installation des dépendances PHP
composer install

# Installation des dépendances JS
npm install

# Configuration de l'environnement
cp .env.example .env
php artisan key:generate

# Migration de la base de données
php artisan migrate

# Compilation des assets
npm run build

# Lancement du serveur
php artisan serve
```

### 8.4 Scripts Disponibles
- `composer setup` : Installation complète
- `composer dev` : Mode développement (serveur + queue + logs + vite)
- `composer test` : Exécution des tests

---

## 9. Tests

### 9.1 Tests Unitaires
- Tests des modèles
- Tests des contrôleurs
- Tests des helpers
- Tests des validations

### 9.2 Tests d'Intégration
- Tests des flux complets
- Tests des permissions
- Tests des rapports
- Tests du système POS

### 9.3 Tests Fonctionnels
- Tests E2E avec BrowserKit
- Tests des formulaires
- Tests des API
- Tests de l'interface

---

## 10. Documentation

### 10.1 Guides Techniques
- **COMPONENTS_GUIDE.md** : Guide des composants UI
- **PERMISSIONS_GUIDE.md** : Guide du système de permissions
- **README.md** : Documentation générale

### 10.2 Commentaires de Code
- Documentation PHPDoc
- Commentaires explicatifs
- Annotations de type

### 10.3 Guides Utilisateur
- Guide d'installation
- Guide d'utilisation
- Guide des permissions
- FAQ

---

## 11. Maintenance

### 11.1 Mises à Jour
- Mises à jour des dépendances
- Corrections de bugs
- Améliorations de performance
- Nouvelles fonctionnalités

### 11.2 Sauvegardes
- Sauvegarde de la base de données
- Sauvegarde des fichiers
- Plan de reprise d'activité
- Tests de restauration

### 11.3 Monitoring
- Surveillance des performances
- Alertes d'erreurs
- Analyse des logs
- Rapports d'utilisation

---

## 12. Calendrier Prévisionnel

### Phase 1 : Développement (Terminé)
- ✅ Architecture de base
- ✅ Modèles de données
- ✅ Authentification
- ✅ Gestion des produits
- ✅ Gestion des stocks
- ✅ Système POS
- ✅ Rapports

### Phase 2 : Tests et Optimisation (En cours)
- ⏳ Tests unitaires
- ⏳ Tests d'intégration
- ⏳ Optimisation des performances
- ⏳ Documentation

### Phase 3 : Déploiement (À venir)
- ⏳ Configuration de production
- ⏳ Mise en ligne
- ⏳ Formation des utilisateurs
- ⏳ Support

---

## 13. Conclusion

Le projet **Gestion de Magasin** est une solution complète et professionnelle pour la gestion de stocks et de ventes. Il offre une interface moderne, des fonctionnalités avancées et une architecture robuste. Le système de permissions garantit une sécurité optimale, tandis que les rapports détaillés permettent une prise de décision éclairée.

L'application est prête pour être déployée en production et peut être étendue avec de nouvelles fonctionnalités selon les besoins.

---

## 14. Annexes

### 14.1 Glossaire
- **POS** : Point of Sale (Système de caisse)
- **CRUD** : Create, Read, Update, Delete
- **ORM** : Object-Relational Mapping
- **API** : Application Programming Interface
- **PDF** : Portable Document Format

### 14.2 Références
- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Bootstrap](https://getbootstrap.com/docs)
- [Documentation Alpine.js](https://alpinejs.dev)
- [Documentation TailwindCSS](https://tailwindcss.com)

### 14.3 Contacts
- **Développeur** : ADOMGNOYAROU
- **Repository** : https://github.com/ADOMGNOYAROU/gestion-de-magasin
- **Support** : À définir

---

*Document version 1.0 - Juin 2026*
