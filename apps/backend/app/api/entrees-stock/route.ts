import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { entreeStockSchema } from '@/lib/schemas/entree-stock';

interface EntreeStockDoc {
  produitId: string;
  magasinId: string;
  fournisseurId: string | null;
  partenaireId: string | null;
  userId: string;
  quantite: number;
  prixUnitaire: number;
  montantTotal: number;
  dateEntree: string;
  numeroBon: string | null;
  notes: string | null;
}

interface ProduitDoc {
  prixVente: number;
}

interface StockMagasinDoc {
  magasinId: string;
  produitId: string;
  quantite: number;
  prixVente: number | null;
  seuilAlerte: number;
}

// Reprend EntreeStockController::index() (50 dernières entrées).
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('entreesStock').orderBy('dateEntree', 'desc').limit(50).get();
  const entrees = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as EntreeStockDoc;
      const [produit, magasin, fournisseur, partenaire] = await Promise.all([
        resolveField('produits', data.produitId, 'nom'),
        resolveField('magasins', data.magasinId, 'nom'),
        resolveField('fournisseurs', data.fournisseurId, 'nom'),
        resolveField('partenaires', data.partenaireId, 'nom'),
      ]);
      return {
        id: doc.id,
        produit,
        magasin,
        fournisseur,
        partenaire,
        quantite: data.quantite,
        prix_unitaire: data.prixUnitaire,
        montant_total: data.montantTotal,
        date_entree: data.dateEntree,
        numero_bon: data.numeroBon,
      };
    }),
  );

  return Response.json(entrees);
});

// Reprend EntreeStockController::store() : création de l'entrée + incrément
// atomique du stock magasin (stocksMagasin/{magasinId}_{produitId}), dans une
// transaction Firestore équivalente au DB::transaction() Laravel.
//
// TODO(Phase 3) : porter StockAlertNotifier (notification admin/gestionnaire
// quand le stock franchit le seuil d'alerte) une fois la collection
// `notifications` conçue.
export const POST = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'gestionnaire');
  const body = entreeStockSchema.parse(await request.json());

  const db = getDb();
  const produitRef = db.collection('produits').doc(body.produitId);
  const stockRef = db.collection('stocksMagasin').doc(`${body.magasinId}_${body.produitId}`);
  const entreeRef = db.collection('entreesStock').doc();

  const montantTotal = body.quantite * body.prixUnitaire;
  const dateEntree = body.dateEntree ?? new Date().toISOString().slice(0, 10);

  await db.runTransaction(async (tx) => {
    const [produitSnap, stockSnap] = await Promise.all([tx.get(produitRef), tx.get(stockRef)]);
    if (!produitSnap.exists) {
      throw new ApiError(422, 'Produit introuvable.');
    }
    const produit = produitSnap.data() as ProduitDoc;

    const entree: EntreeStockDoc = {
      produitId: body.produitId,
      magasinId: body.magasinId,
      fournisseurId: body.fournisseurId ?? null,
      partenaireId: body.partenaireId ?? null,
      userId: authUser.uid,
      quantite: body.quantite,
      prixUnitaire: body.prixUnitaire,
      montantTotal,
      dateEntree,
      numeroBon: body.numeroBon ?? null,
      notes: body.notes ?? null,
    };
    tx.set(entreeRef, entree);

    if (stockSnap.exists) {
      const stock = stockSnap.data() as StockMagasinDoc;
      tx.update(stockRef, { quantite: stock.quantite + body.quantite });
    } else {
      const newStock: StockMagasinDoc = {
        magasinId: body.magasinId,
        produitId: body.produitId,
        quantite: body.quantite,
        prixVente: produit.prixVente,
        seuilAlerte: 10,
      };
      tx.set(stockRef, newStock);
    }
  });

  return Response.json({ id: entreeRef.id }, { status: 201 });
});
