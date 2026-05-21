# Guide de Déploiement SaaS - Gestion de Magasin

Ce guide explique comment déployer l'application Gestion de Magasin en tant que solution SaaS avec abonnements Stripe.

## 📋 Prérequis

- PHP 8.2 ou supérieur
- Composer
- SQLite, MySQL ou PostgreSQL
- Compte Stripe (pour les paiements)
- Serveur web (Apache/Nginx) ou Laravel Forge

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone <votre-repo>
cd gestion-magasin
```

### 2. Installer les dépendances

```bash
composer install
npm install
npm run build
```

### 3. Configuration de l'environnement

Copier le fichier `.env.example` vers `.env` :

```bash
cp .env.example .env
```

Configurer les variables d'environnement :

```env
APP_NAME="Gestion Magasin SaaS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_magasin
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

# Stripe Configuration
STRIPE_KEY=pk_test_votre_clé_publique
STRIPE_SECRET=sk_test_votre_clé_secrète
STRIPE_WEBHOOK_SECRET=whsec_votre_webhook_secret
STRIPE_CURRENCY=eur

# Stripe Price IDs (à créer dans Stripe Dashboard)
STRIPE_PRICE_STARTER=price_starter_monthly_id
STRIPE_PRICE_STARTER_YEARLY=price_starter_yearly_id
STRIPE_PRICE_PRO=price_pro_monthly_id
STRIPE_PRICE_PRO_YEARLY=price_pro_yearly_id
STRIPE_PRICE_ENTERPRISE=price_enterprise_monthly_id
STRIPE_PRICE_ENTERPRISE_YEARLY=price_enterprise_yearly_id
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Exécuter les migrations

```bash
php artisan migrate
```

### 6. Créer les produits et prix dans Stripe

Connectez-vous à votre [Dashboard Stripe](https://dashboard.stripe.com/) et créez les produits suivants :

#### Plan Starter
- **Nom** : Starter
- **Prix mensuel** : 29€
- **Prix annuel** : 290€
- **Description** : Idéal pour les petites entreprises

#### Plan Pro
- **Nom** : Pro
- **Prix mensuel** : 79€
- **Prix annuel** : 790€
- **Description** : Pour les entreprises en croissance

#### Plan Enterprise
- **Nom** : Enterprise
- **Prix mensuel** : 199€
- **Prix annuel** : 1990€
- **Description** : Solution complète pour grandes entreprises

Copiez les IDs des prix créés et mettez-les dans votre fichier `.env`.

### 7. Configurer le Webhook Stripe

1. Dans le Dashboard Stripe, allez dans **Developers > Webhooks**
2. Cliquez sur **Add endpoint**
3. URL du webhook : `https://votre-domaine.com/stripe/webhook`
4. Sélectionnez les événements suivants :
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
5. Copiez le **Signing Secret** et mettez-le dans `STRIPE_WEBHOOK_SECRET`

### 8. Lancer le seeder de démonstration (optionnel)

```bash
php artisan db:seed --class=DemoTenantSeeder
```

Cela créera un tenant de démonstration avec 3 utilisateurs :
- **Admin** : admin@entreprise.com / password
- **Gestionnaire** : gestionnaire@entreprise.com / password
- **Vendeur** : vendeur@entreprise.com / password

### 9. Optimiser l'application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔐 Configuration de la sécurité

### 1. HTTPS

En production, HTTPS est obligatoire pour les paiements Stripe. Configurez votre serveur avec un certificat SSL valide.

### 2. Middleware

Les middlewares suivants sont déjà configurés dans `bootstrap/app.php` :

- `tenant` : Identifie le tenant courant
- `subscription` : Vérifie l'abonnement actif
- `plan.limits` : Vérifie les limites du plan

### 3. Protection des routes

Les routes sensibles sont protégées par les middlewares appropriés :

```php
Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    // Routes protégées
});
```

## 📊 Architecture Multi-Tenant

### Isolation des données

Chaque tenant a ses propres données isolées grâce au système de scopes globaux sur les modèles :

```php
// Dans chaque modèle
protected static function booted()
{
    static::addGlobalScope('tenant', function ($query) {
        if (auth()->check() && auth()->user()->tenant_id) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }
    });
}
```

### Tables avec tenant_id

Les tables suivantes incluent `tenant_id` :
- users
- magasins
- boutiques
- produits
- stock_magasins
- stock_boutiques
- fournisseurs
- partenaires
- entree_stocks
- transferts
- ventes
- vente_produits
- clients
- credits
- credit_payments
- orders
- order_items
- payment_methods
- cash_register_sessions

## 💳 Gestion des Abonnements

### Cycle de vie d'un abonnement

1. **Inscription** : L'utilisateur crée un compte et un tenant
2. **Essai gratuit** : 14 jours d'essai gratuit par défaut
3. **Souscription** : L'utilisateur ajoute une méthode de paiement
4. **Renouvellement** : Paiement automatique mensuel
5. **Expiration** : Si le paiement échoue, l'accès est bloqué
6. **Réactivation** : L'utilisateur peut réactiver son abonnement

### Gestion des limites

Le middleware `CheckPlanLimits` vérifie automatiquement les limites du plan :

- **Starter** : 3 utilisateurs, 1 magasin, 3 boutiques, 500 produits
- **Pro** : 10 utilisateurs, 3 magasins, 10 boutiques, 2000 produits
- **Enterprise** : Illimité

## 🧪 Tests

### Tester localement

1. Utilisez les clés de test Stripe (`pk_test_...`, `sk_test_...`)
2. Utilisez les cartes de test Stripe pour simuler les paiements
3. Testez les scénarios suivants :
   - Inscription réussie
   - Échec de paiement
   - Upgrade de plan
   - Downgrade de plan
   - Annulation d'abonnement

### Cartes de test Stripe

- **Succès** : 4242 4242 4242 4242
- **Échec** : 4000 0000 0000 0002
- **Exigence 3D Secure** : 4000 0025 0000 3155

## 📈 Monitoring

### Logs

Les logs sont stockés dans `storage/logs/laravel.log`. Surveillez :
- Erreurs de paiement Stripe
- Tentatives d'accès non autorisées
- Limites de plan atteintes

### Métriques à surveiller

- Taux de conversion (inscription → abonnement)
- Churn rate (taux d'annulation)
- Revenue per user
- Utilisation des fonctionnalités par plan

## 🔄 Mises à jour

### Mise à jour de l'application

```bash
git pull origin main
composer install
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Mise à jour des plans

Modifiez `config/plans.php` pour ajuster les plans et les limites.

## 🆘 Support

### Problèmes courants

**Erreur de paiement Stripe**
- Vérifiez que les clés API sont correctes
- Vérifiez que le webhook est configuré
- Consultez les logs Stripe

**Accès refusé**
- Vérifiez que l'utilisateur a un tenant_id
- Vérifiez que l'abonnement est actif
- Vérifiez que les limites du plan ne sont pas atteintes

**Middleware non appliqué**
- Vérifiez que les middlewares sont enregistrés dans `bootstrap/app.php`
- Vérifiez que les routes utilisent les bons middlewares

## 📚 Ressources

- [Documentation Laravel Cashier](https://laravel.com/docs/cashier)
- [Documentation Stripe](https://stripe.com/docs)
- [Guide Laravel SaaS](https://laravel.com/docs/billing)

## 🎯 Checklist de déploiement

- [ ] Clés Stripe configurées
- [ ] Produits et prix créés dans Stripe
- [ ] Webhook Stripe configuré
- [ ] Migrations exécutées
- [ ] Cache optimisé
- [ ] HTTPS configuré
- [ ] Middleware enregistrés
- [ ] Routes testées
- [ ] Paiements testés
- [ ] Limites de plan testées
- [ ] Monitoring configuré

---

**Note** : Pour un environnement de production, utilisez Laravel Forge ou un service d'hébergement similaire pour un déploiement automatisé et sécurisé.
