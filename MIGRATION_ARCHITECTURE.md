# Architecture Cible — Migration Angular / Next.js / Firestore

Document de référence de la **Phase 0** du plan de migration. Il fige le modèle de données Firestore, le contrat d'API et le modèle de sécurité qui serviront de base à toutes les phases suivantes (Next.js, Angular, migration des données).

Source d'analyse : 15 modèles Eloquent, 36 migrations, `routes/api.php` (API Sanctum déjà existante) et `routes/web.php` de l'application Laravel actuelle.

---

## 1. Vue d'ensemble

```
Angular (SPA)  --HTTPS/JSON-->  Next.js API (Route Handlers)  --Admin SDK-->  Firestore
     |                                   |
     +-------- Firebase Auth (ID token) -+
```

- **Angular** ne parle jamais directement à Firestore. Il s'authentifie via Firebase Auth (client SDK) pour obtenir un ID token, puis appelle l'API Next.js avec ce token en `Authorization: Bearer`.
- **Next.js** est utilisé en mode API pure (Route Handlers sous `/app/api/**`), pas de pages SSR côté data — toute la logique métier (équivalent des Controllers/Services Laravel actuels) y vit. Il utilise le **Firebase Admin SDK**, qui contourne les règles de sécurité Firestore : c'est donc le code Next.js qui porte toute l'autorisation métier (rôle, périmètre magasin/boutique), exactement comme le fait aujourd'hui le middleware Laravel + les Gates.
- **Firestore** ne reçoit donc quasiment aucune règle de sécurité complexe côté client (accès direct client → Firestore fermé par défaut). Ça simplifie beaucoup la Phase 0 par rapport à une architecture "Angular + Firestore direct".
- **Render** héberge 2 services web séparés : `frontend` (Angular) et `backend` (Next.js), tous deux dans le même repo monorepo.

Ce choix reproduit fidèlement l'architecture actuelle (Blade+API Sanctum → devient Angular+API Next.js), ce qui limite le risque de régression fonctionnelle.

---

## 2. Modèle de données Firestore

Principe général : les tables MySQL avec clé étrangère simple deviennent des collections top-level avec des champs `xxxId` (référence par ID, pas de `DocumentReference` imbriqué, pour rester simple à sérialiser en JSON côté API). Les tables de liaison "toujours accédées avec leur parent" (`vente_produits`) deviennent des **sous-collections**. Les tables avec **contrainte d'unicité métier** (`stock_magasins`, `stock_boutiques`) utilisent un **ID composite déterministe**, ce qui remplace la contrainte unique SQL et permet des transactions atomiques simples (lecture/écriture par ID direct, sans requête).

### 2.1 `users/{uid}`
`uid` = UID Firebase Auth (remplace l'auto-increment + Sanctum).

| Champ | Type | Origine |
|---|---|---|
| name | string | users.name |
| email | string | users.email (dupliqué depuis Firebase Auth pour requêtage facile) |
| role | `'admin'\|'gestionnaire'\|'vendeur'` | users.role |
| magasinId | string\|null | users.magasin_id (magasin dont il est responsable) |
| boutiqueId | string\|null | users.boutique_id (boutique où il vend) |
| createdAt | timestamp | — |

> Le mot de passe n'existe plus ici : géré entièrement par Firebase Auth.

### 2.2 `magasins/{magasinId}`
| Champ | Type |
|---|---|
| nom | string |
| localisation | string\|null |
| responsableId | string (ref users) \| null |
| adresse, telephone, email | string\|null *(nullable déjà en base actuelle)* |

### 2.3 `boutiques/{boutiqueId}`
| Champ | Type |
|---|---|
| nom, adresse, telephone | string |
| email | string\|null |
| magasinId | string (ref magasins) |
| vendeurId | string\|null (ref users) |

### 2.4 `produits/{produitId}`
| Champ | Type |
|---|---|
| nom, categorie, description | string |
| prixAchat, prixVente | number |
| prixAncien | number\|null |
| reference | string\|null |
| statut | string |
| deletedAt | timestamp\|null *(soft delete applicatif — Firestore n'a pas de soft delete natif, on filtre `deletedAt == null` dans les requêtes)* |

`margeAttribute`/`margePercentage` : recalculés côté API à la volée (comme aujourd'hui, ce sont des accesseurs Eloquent, pas des colonnes).

### 2.5 `stocksMagasin/{magasinId}_{produitId}` *(ID composite)*
| Champ | Type |
|---|---|
| magasinId, produitId | string |
| quantite | number |
| prixVente | number\|null |
| seuilAlerte | number |

### 2.6 `stocksBoutique/{boutiqueId}_{produitId}` *(ID composite)*
Mêmes champs que `stocksMagasin`, avec `boutiqueId` au lieu de `magasinId`.

> **Pourquoi un ID composite plutôt qu'une simple collection + requête ?** Le POS et les transferts doivent décrémenter/incrémenter une quantité de façon atomique et concurrente (plusieurs ventes simultanées sur la même boutique). Avec un ID déterministe, on fait un `runTransaction` par lecture/écriture directe du document, sans passer par une requête (`where produitId == X and boutiqueId == Y`), ce qui est indispensable pour garantir l'atomicité en Firestore (les transactions Firestore ne verrouillent pas les résultats de requêtes, seulement les documents lus explicitement par référence).

### 2.7 `entreesStock/{id}`
| Champ | Type |
|---|---|
| produitId, magasinId, fournisseurId, partenaireId, userId | string |
| quantite | number |
| prixUnitaire, montantTotal | number |
| dateEntree | timestamp |

Écriture : transaction qui crée le document **et** incrémente `stocksMagasin/{magasinId}_{produitId}.quantite` (créé si absent).

### 2.8 `transferts/{id}`
| Champ | Type |
|---|---|
| produitId, magasinId, boutiqueId | string |
| quantite | number |
| date | timestamp |
| notes | string\|null |

Écriture : transaction qui vérifie la dispo dans `stocksMagasin`, décrémente `stocksMagasin`, incrémente `stocksBoutique` (création si absent).

### 2.9 `ventes/{id}`
| Champ | Type |
|---|---|
| boutiqueId, userId | string |
| sessionCaisseId | string |
| paymentMethodId | string |
| montantTotal, montantRecu, monnaie | number |
| numeroTicket | string *(généré côté API, même logique `TKT-YYYYMMDD-NNNN`)* |
| status | `'en_cours'\|'terminee'\|'annulee'` |
| dateVente | timestamp |
| notes | string\|null |

**Sous-collection `ventes/{id}/produits/{lineId}`** *(remplace `vente_produits`)* :

| Champ | Type |
|---|---|
| produitId | string |
| quantite | number |
| prixUnitaire, remise, remisePourcentage, sousTotal | number |

Checkout POS = **une seule transaction Firestore** qui : lit tous les `stocksBoutique/{boutiqueId}_{produitId}` concernés, vérifie la dispo, les décrémente, crée la vente + ses lignes, et met à jour `montantTheorique` de la session de caisse. C'est le point le plus critique de toute la migration : à traiter en priorité en Phase 3 avec des tests de concurrence dédiés.

### 2.10 `cashRegisterSessions/{id}`
| Champ | Type |
|---|---|
| vendeurId, boutiqueId | string |
| montantInitial, montantFinal, montantTheorique, ecart | number\|null |
| dateOuverture, dateFermeture | timestamp\|null |
| status | `'ouverte'\|'en_cours'\|'fermee'` |
| notes | string\|null |

### 2.11 `paymentMethods/{id}`
| Champ | Type |
|---|---|
| name, code, description | string |
| isActive | boolean |

### 2.12 `fournisseurs/{id}` / `partenaires/{id}`
| Champ | Type |
|---|---|
| nom, adresse, telephone, email | string |
| contactPersonne *(fournisseur)* / typePartenariat *(partenaire)* | string\|null |

### 2.13 `mobileMoneyPayments/{id}`
| Champ | Type |
|---|---|
| userId, venteId | string |
| identifier, txReference, paymentReference | string |
| phoneNumber, network | string |
| amount | number |
| status | string |
| paidAt | timestamp\|null |
| rawResponse | map |

### 2.14 `notifications/{id}`
| Champ | Type |
|---|---|
| userId | string |
| type | string |
| data | map |
| readAt | timestamp\|null |

### 2.15 Ce qui **disparaît** dans la migration
- `sessions` (sessions PHP) et `personal_access_tokens` (Sanctum) → remplacés par les ID tokens Firebase Auth, stateless, rien à stocker.
- `cache`, `jobs` → à réévaluer en Phase 3 seulement si un besoin de file d'attente apparaît (ex. envoi asynchrone de webhooks Mobile Money) ; sinon pas d'équivalent Firestore nécessaire au départ.

### 2.16 Index composites à prévoir (Firestore)
- `ventes` : (`boutiqueId`, `dateVente`), (`userId`, `dateVente`), (`sessionCaisseId`), (`status`, `dateVente`)
- `stocksBoutique` : (`boutiqueId`, `quantite`) — pour les alertes de stock bas
- `stocksMagasin` : (`magasinId`, `quantite`)
- `entreesStock` : (`magasinId`, `dateEntree`)
- `transferts` : (`magasinId`, `date`), (`boutiqueId`, `date`)

### 2.17 Rapports & agrégations
Firestore n'a pas d'équivalent à `SUM()/GROUP BY` SQL. Pour la Phase 3, on calcule les rapports (CA, produits les plus vendus, dashboard) par requêtes indexées + agrégation côté Next.js (acceptable au volume d'une PME multi-boutiques). Si la volumétrie ou la lenteur devient un problème en prod, prévoir en évolution : export Firestore → BigQuery, ou compteurs dénormalisés maintenus par Cloud Functions (ex. `stats/dashboard` mis à jour à chaque vente). **Ne pas construire ça dès la Phase 3** — seulement si un besoin réel est observé.

---

## 3. Rôles & sécurité

- Rôles `admin | gestionnaire | vendeur` stockés en **Firebase custom claims** (`role`, `magasinId`, `boutiqueId`), posés côté serveur (Admin SDK) à la création/modification d'un utilisateur — jamais modifiables par le client.
- Chaque route Next.js vérifie le token (Admin SDK `verifyIdToken`), lit les claims, et applique la même matrice de permissions que `PERMISSIONS_GUIDE.md` actuel (ex : `produits`/`boutiques`/`fournisseurs`/`partenaires`/`entrees-stock`/`transferts` → admin+gestionnaire ; `ventes`/POS → tous rôles authentifiés ; `users`/`magasins` → admin uniquement).
- Règles de sécurité Firestore : **deny-all** pour les clients (lecture/écriture uniquement via l'Admin SDK côté Next.js). Firebase Auth reste le seul point de contact direct entre Angular et Firebase.

---

## 4. Contrat API Next.js

Reprend à l'identique la surface de `routes/api.php` actuelle (déjà une bonne API REST stateless via Sanctum) — c'est un des points qui réduit le risque de cette migration : **pas de redesign du contrat, seulement un changement d'implémentation**.

| Domaine | Endpoints (méthode + path) | Rôles |
|---|---|---|
| Auth | `POST /api/login`, `POST /api/register`, `GET /api/user`, `POST /api/logout` | public / auth |
| Dashboard | `GET /api/dashboard` | auth |
| Produits | `GET/POST/PUT/DELETE /api/produits`, `POST /api/produits/:id/restore` | gestionnaire+ |
| Magasins | CRUD `/api/magasins` | admin |
| Boutiques | CRUD `/api/boutiques` | gestionnaire+ |
| Fournisseurs / Partenaires | CRUD `/api/fournisseurs`, `/api/partenaires` | gestionnaire+ |
| Users | CRUD `/api/users` | admin |
| Stocks | `GET /api/stock-magasins`, `GET /api/stock-boutiques` | gestionnaire+ |
| Entrées stock | `GET/POST /api/entrees-stock` | gestionnaire+ |
| Transferts | `GET/POST /api/transferts`, `GET /api/stock-disponible` | gestionnaire+ |
| Caisse | `GET /api/cash-register-session/current`, `POST /api/cash-register-session/open`, `POST /api/cash-register-session/:id/close` | vendeur+ |
| Ventes / POS | `GET/POST /api/ventes` | vendeur+ |
| Paiement mobile | `POST /api/mobile-money/pay`, `GET /api/mobile-money/:id/status`, `GET /api/mobile-money/balance`, webhook `POST /api/webhooks/paygate` (public) | vendeur+ / public (webhook signé) |
| Notifications | `GET /api/notifications`, `GET /api/notifications/unread-count`, `POST /api/notifications/:id/read`, `POST /api/notifications/read-all` | auth |
| Rapports | à ajouter (actuellement seulement côté web Blade : PDF stock, PDF/Excel ventes, PDF partenaires) → nouveaux endpoints `GET /api/rapports/stock`, `POST /api/rapports/ventes`, `GET /api/rapports/partenaires` retournant soit du JSON (affiché par Angular) soit un flux PDF/Excel généré via `pdf-lib`/`puppeteer` + `exceljs` | gestionnaire+ |
| Panier POS | Le panier actuel (`/api/panier/*`) est géré côté session serveur Laravel → en Angular, le panier devient un **état client** (service Angular), envoyé en un seul payload à `POST /api/ventes` (checkout). Plus besoin d'endpoints panier côté API. | — |

> Point de vigilance intégration existante : le **webhook PayGateGlobal** (`/api/webhooks/paygate`) doit rester une route publique joignable depuis Internet — à bien exposer sur le service Render `backend` en prod dès la Phase 1 (URL stable requise pour la config PayGateGlobal).

---

## 5. Risques identifiés à surveiller activement

1. **Atomicité du POS** — la transaction de checkout (multi-documents stocksBoutique + vente + session caisse) est le composant le plus critique. Prototype + tests de charge dès le début de la Phase 3, pas à la fin.
2. **Rapports/agrégations** — pas de `SUM/GROUP BY` Firestore ; à calculer côté Next.js, revoir si lenteur observée en usage réel.
3. **Génération PDF/Excel** — remplacer `barryvdh/laravel-dompdf` et `maatwebsite/excel` par des libs Node équivalentes (à choisir en Phase 3 : `pdf-lib`/`puppeteer`, `exceljs`).
4. **Limite des transactions Firestore** — 500 opérations max par transaction : sans impact prévisible ici (paniers POS de quelques lignes), à garder en tête si un jour un import en masse est fait sans le script dédié de la Phase 6.
5. **Webhook Mobile Money** — nécessite une URL publique stable dès la mise en service du service Render backend (à ne pas oublier en Phase 1/8).

---

## 6. Prochaine étape

Phase 1 : mise en place du monorepo, du projet Firebase et des 2 services Render (staging), avec un déploiement "hello world" de bout en bout avant d'écrire la moindre logique métier.
