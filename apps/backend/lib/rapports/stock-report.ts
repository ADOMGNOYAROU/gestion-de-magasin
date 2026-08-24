import type { AuthUser } from '../auth';
import { getDb } from '../firebase-admin';

interface ProduitDoc {
  nom: string;
  categorie: string;
  prixVente: number;
  statut: string;
  deletedAt: string | null;
}

interface StockDoc {
  produitId: string;
  magasinId?: string;
  boutiqueId?: string;
  quantite: number;
  seuilAlerte: number;
}

export interface StockReportRow {
  produitId: string;
  nom: string;
  categorie: string;
  prixVente: number;
  stockMagasin: number;
  stockBoutique: number;
  seuilAlerteRef: number;
}

export interface StockDetailRow extends StockDoc {
  produitNom: string;
}

export interface StockReportData {
  scope: { type: 'admin' | 'magasin' } | { type: 'boutique' };
  scopeNom: string | null;
  rows: StockReportRow[];
  detailParMagasin: Map<string, { nom: string; stocks: StockDetailRow[] }>;
  detailParBoutique: Map<string, { nom: string; magasinNom: string; stocks: StockDetailRow[] }>;
}

function chunk<T>(arr: T[], size: number): T[][] {
  const out: T[][] = [];
  for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
  return out;
}

// Reprend RapportController::rapportStockPDF(). La branche vendeur du contrôleur
// Laravel n'est jamais atteignable (routes/web.php restreint cette route au
// middleware 'gestionnaire', donc admin+gestionnaire uniquement) : non reprise ici.
export async function buildStockReportData(authUser: AuthUser): Promise<StockReportData> {
  const db = getDb();

  if (authUser.role === 'gestionnaire') {
    if (!authUser.magasinId) {
      return {
        scope: { type: 'magasin' },
        scopeNom: null,
        rows: [],
        detailParMagasin: new Map(),
        detailParBoutique: new Map(),
      };
    }

    const [magasinDoc, stocksMagasinSnap, boutiquesSnap] = await Promise.all([
      db.collection('magasins').doc(authUser.magasinId).get(),
      db.collection('stocksMagasin').where('magasinId', '==', authUser.magasinId).get(),
      db.collection('boutiques').where('magasinId', '==', authUser.magasinId).get(),
    ]);

    const stocksMagasin = stocksMagasinSnap.docs.map((d) => d.data() as StockDoc);
    const boutiqueIds = boutiquesSnap.docs.map((d) => d.id);

    const stocksBoutique: StockDoc[] = [];
    for (const batch of chunk(boutiqueIds, 30)) {
      if (batch.length === 0) continue;
      const snap = await db.collection('stocksBoutique').where('boutiqueId', 'in', batch).get();
      stocksBoutique.push(...snap.docs.map((d) => d.data() as StockDoc));
    }

    const produitIds = [...new Set(stocksMagasin.map((s) => s.produitId))];
    const produits = new Map<string, ProduitDoc>();
    for (const id of produitIds) {
      const doc = await db.collection('produits').doc(id).get();
      if (doc.exists) produits.set(id, doc.data() as ProduitDoc);
    }

    const rows: StockReportRow[] = produitIds
      .map((id) => {
        const produit = produits.get(id);
        if (!produit || produit.statut !== 'actif' || produit.deletedAt) return null;
        const sm = stocksMagasin.find((s) => s.produitId === id);
        return {
          produitId: id,
          nom: produit.nom,
          categorie: produit.categorie,
          prixVente: produit.prixVente,
          stockMagasin: sm?.quantite ?? 0,
          stockBoutique: 0,
          seuilAlerteRef: sm?.seuilAlerte ?? 0,
        };
      })
      .filter((r): r is StockReportRow => r !== null)
      .sort((a, b) => a.nom.localeCompare(b.nom));

    return {
      scope: { type: 'magasin' },
      scopeNom: magasinDoc.exists ? (magasinDoc.data() as { nom: string }).nom : null,
      rows,
      detailParMagasin: new Map(),
      detailParBoutique: new Map(),
    };
  }

  // admin
  const [produitsSnap, stocksMagasinSnap, stocksBoutiqueSnap, magasinsSnap, boutiquesSnap] = await Promise.all([
    db.collection('produits').where('statut', '==', 'actif').where('deletedAt', '==', null).get(),
    db.collection('stocksMagasin').get(),
    db.collection('stocksBoutique').get(),
    db.collection('magasins').get(),
    db.collection('boutiques').get(),
  ]);

  const magasinsById = new Map(magasinsSnap.docs.map((d) => [d.id, (d.data() as { nom: string }).nom]));
  const boutiquesById = new Map(
    boutiquesSnap.docs.map((d) => [d.id, d.data() as { nom: string; magasinId: string }]),
  );
  const produitsById = new Map(produitsSnap.docs.map((d) => [d.id, (d.data() as ProduitDoc).nom]));

  const stocksMagasin = stocksMagasinSnap.docs.map((d) => d.data() as StockDoc);
  const stocksBoutique = stocksBoutiqueSnap.docs.map((d) => d.data() as StockDoc);

  const rows: StockReportRow[] = produitsSnap.docs
    .map((doc) => {
      const produit = doc.data() as ProduitDoc;
      const sm = stocksMagasin.filter((s) => s.produitId === doc.id);
      const sb = stocksBoutique.filter((s) => s.produitId === doc.id);
      return {
        produitId: doc.id,
        nom: produit.nom,
        categorie: produit.categorie,
        prixVente: produit.prixVente,
        stockMagasin: sm.reduce((sum, s) => sum + s.quantite, 0),
        stockBoutique: sb.reduce((sum, s) => sum + s.quantite, 0),
        seuilAlerteRef: sm[0]?.seuilAlerte ?? sb[0]?.seuilAlerte ?? 0,
      };
    })
    .sort((a, b) => a.nom.localeCompare(b.nom));

  const detailParMagasin = new Map<string, { nom: string; stocks: StockDetailRow[] }>();
  for (const s of stocksMagasin) {
    if (!s.magasinId) continue;
    const nom = magasinsById.get(s.magasinId) ?? s.magasinId;
    if (!detailParMagasin.has(s.magasinId)) detailParMagasin.set(s.magasinId, { nom, stocks: [] });
    detailParMagasin.get(s.magasinId)!.stocks.push({ ...s, produitNom: produitsById.get(s.produitId) ?? s.produitId });
  }

  const detailParBoutique = new Map<string, { nom: string; magasinNom: string; stocks: StockDetailRow[] }>();
  for (const s of stocksBoutique) {
    if (!s.boutiqueId) continue;
    const boutique = boutiquesById.get(s.boutiqueId);
    const nom = boutique?.nom ?? s.boutiqueId;
    const magasinNom = boutique ? (magasinsById.get(boutique.magasinId) ?? '') : '';
    if (!detailParBoutique.has(s.boutiqueId)) detailParBoutique.set(s.boutiqueId, { nom, magasinNom, stocks: [] });
    detailParBoutique
      .get(s.boutiqueId)!
      .stocks.push({ ...s, produitNom: produitsById.get(s.produitId) ?? s.produitId });
  }

  return { scope: { type: 'admin' }, scopeNom: null, rows, detailParMagasin, detailParBoutique };
}
