# Guide de Configuration Paystack et Flutterwave

Ce guide explique comment configurer Paystack et Flutterwave pour les paiements SaaS au Togo et en Afrique.

## 📋 Pourquoi Paystack et Flutterwave ?

### Paystack
- ✅ Disponible au Togo et dans toute l'Afrique de l'Ouest
- ✅ Supporte Mobile Money (MTN Money, Orange Money)
- ✅ Frais : 1.5% + 100 FCFA par transaction
- ✅ API Laravel facile à intégrer
- ✅ Documentation excellente

### Flutterwave
- ✅ Disponible au Togo et dans toute l'Afrique
- ✅ Supporte Mobile Money (MTN, Orange, Moov)
- ✅ Frais : 1.4% + 500 FCFA par transaction
- ✅ API Laravel disponible
- ✅ Supporte plusieurs devises africaines

## 🚀 Installation

Les services Paystack et Flutterwave sont déjà intégrés dans l'application via des services personnalisés.

### Services Créés

1. **PaystackService** (`app/Services/PaystackService.php`)
   - Création de clients
   - Initialisation de transactions
   - Vérification de transactions
   - Gestion des abonnements

2. **FlutterwaveService** (`app/Services/FlutterwaveService.php`)
   - Création de clients
   - Initialisation de transactions
   - Vérification de transactions
   - Gestion des abonnements

## ⚙️ Configuration

### 1. Configuration Paystack

Créez un compte Paystack : https://dashboard.paystack.co/

```env
# Dans .env
PAYSTACK_PUBLIC_KEY=pk_test_votre_clé_publique
PAYSTACK_SECRET_KEY=sk_test_votre_clé_secrète
PAYSTACK_PAYMENT_URL=https://api.paystack.co

# Codes des plans (à créer dans Paystack Dashboard)
PAYSTACK_PLAN_STARTER=PLN_starter_monthly
PAYSTACK_PLAN_PRO=PLN_pro_monthly
PAYSTACK_PLAN_ENTERPRISE=PLN_enterprise_monthly
```

### 2. Configuration Flutterwave

Créez un compte Flutterwave : https://dashboard.flutterwave.com/

```env
# Dans .env
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_TEST_votre_clé_publique
FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST_votre_clé_secrète
FLUTTERWAVE_ENCRYPTION_KEY=FLWSECK_TEST_votre_clé_encryption
FLUTTERWAVE_PAYMENT_URL=https://api.flutterwave.com/v3

# IDs des plans (à créer dans Flutterwave Dashboard)
FLUTTERWAVE_PLAN_STARTER=starter_monthly
FLUTTERWAVE_PLAN_PRO=pro_monthly
FLUTTERWAVE_PLAN_ENTERPRISE=enterprise_monthly
```

## 📊 Création des Plans

### Paystack

1. Connectez-vous au Dashboard Paystack
2. Allez dans **Plans** > **Create Plan**
3. Créez les plans suivants :

**Plan Starter**
- Nom : Starter
- Montant : 29000 (en kobo = 290 FCFA)
- Intervalle : Mensuel
- Description : Idéal pour les petites entreprises

**Plan Pro**
- Nom : Pro
- Montant : 79000 (en kobo = 790 FCFA)
- Intervalle : Mensuel
- Description : Pour les entreprises en croissance

**Plan Enterprise**
- Nom : Enterprise
- Montant : 199000 (en kobo = 1990 FCFA)
- Intervalle : Mensuel
- Description : Solution complète pour grandes entreprises

4. Copiez les codes des plans (ex: `PLN_starter_monthly`)
5. Mettez-les dans votre fichier `.env`

### Flutterwave

1. Connectez-vous au Dashboard Flutterwave
2. Allez dans **Payment Plans** > **Create Plan**
3. Créez les plans suivants :

**Plan Starter**
- Nom : Starter
- Montant : 29000
- Devise : XAF (Franc CFA)
- Intervalle : Mensuel
- Description : Idéal pour les petites entreprises

**Plan Pro**
- Nom : Pro
- Montant : 79000
- Devise : XAF
- Intervalle : Mensuel
- Description : Pour les entreprises en croissance

**Plan Enterprise**
- Nom : Enterprise
- Montant : 199000
- Devise : XAF
- Intervalle : Mensuel
- Description : Solution complète pour grandes entreprises

4. Copiez les IDs des plans
5. Mettez-les dans votre fichier `.env`

## 🔄 Utilisation

### Choisir le Provider de Paiement

Le système supporte 3 providers :
- **Stripe** (pour l'Europe)
- **Paystack** (recommandé pour l'Afrique)
- **Flutterwave** (alternative pour l'Afrique)

Pour changer de provider, modifiez le champ `payment_provider` dans la table `tenants` :
- `stripe` : Utiliser Stripe
- `paystack` : Utiliser Paystack
- `flutterwave` : Utiliser Flutterwave

### Routes Disponibles

#### Paystack
- `/paystack` : Dashboard d'abonnement Paystack
- `/paystack/initialize` : Initialiser un paiement
- `/paystack/callback` : Callback après paiement
- `/paystack/cancel` : Annuler l'abonnement
- `/paystack/public-key` : Obtenir la clé publique

#### Flutterwave
- `/flutterwave` : Dashboard d'abonnement Flutterwave
- `/flutterwave/initialize` : Initialiser un paiement
- `/flutterwave/callback` : Callback après paiement
- `/flutterwave/cancel` : Annuler l'abonnement
- `/flutterwave/public-key` : Obtenir la clé publique

### Exemple d'Intégration

Dans votre vue, vous pouvez rediriger vers le bon provider :

```php
// Dans votre contrôleur
$tenant = auth()->user()->tenant;

if ($tenant->payment_provider === 'paystack') {
    return redirect()->route('paystack.show');
} elseif ($tenant->payment_provider === 'flutterwave') {
    return redirect()->route('flutterwave.show');
} else {
    return redirect()->route('subscription.show');
}
```

## 🧪 Test

### Mode Test Paystack

Paystack fournit des cartes de test :
- **Succès** : 50606 00000 0000 0000 0000 0000
- **Échec** : 50606 00000 0000 0000 0000 0001

### Mode Test Flutterwave

Flutterwave fournit des cartes de test :
- **Succès** : 4187427410000406
- **Échec** : 4187427410000407

## 💰 Frais de Transaction

### Paystack
- Frais : 1.5% + 100 FCFA
- Exemple : Transaction de 10 000 FCFA = 150 FCFA + 100 FCFA = 250 FCFA

### Flutterwave
- Frais : 1.4% + 500 FCFA
- Exemple : Transaction de 10 000 FCFA = 140 FCFA + 500 FCFA = 640 FCFA

## 🔐 Sécurité

### Webhooks

Pour une production complète, configurez les webhooks :

#### Paystack Webhook
1. Dans Dashboard Paystack, allez dans **Settings** > **Webhooks**
2. URL : `https://votre-domaine.com/paystack/webhook`
3. Événements à écouter :
   - `charge.success`
   - `invoice.create`
   - `subscription.disable`
   - `subscription.enable`

#### Flutterwave Webhook
1. Dans Dashboard Flutterwave, allez dans **Settings** > **Webhooks**
2. URL : `https://votre-domaine.com/flutterwave/webhook`
3. Événements à écouter :
   - `charge.success`
   - `subscription.create`
   - `subscription.cancel`

## 📱 Mobile Money

### Paystack Mobile Money

Paystack supporte :
- MTN Mobile Money
- Orange Money
- Airtel Money

### Flutterwave Mobile Money

Flutterwave supporte :
- MTN Mobile Money
- Orange Money
- Moov Money
- MobiCash

## 🎯 Recommandation pour le Togo

**Paystack** est recommandé pour le Togo car :
- Meilleure documentation
- Plus stable
- Support local excellent
- Frais légèrement inférieurs pour les petites transactions

## 📚 Documentation Officielle

- [Paystack Documentation](https://paystack.com/docs/)
- [Flutterwave Documentation](https://developer.flutterwave.com/docs/)

## 🆘 Support

En cas de problème :
1. Vérifiez vos clés API dans `.env`
2. Vérifiez que les plans sont créés dans le dashboard
3. Consultez les logs : `storage/logs/laravel.log`
4. Testez en mode test avant de passer en production

---

**Note** : Pour passer en production, remplacez les clés de test par les clés de production dans votre fichier `.env`.
