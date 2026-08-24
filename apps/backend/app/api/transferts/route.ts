import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { transfertSchema } from '@/lib/schemas/transfert';

interface TransfertDoc {
  produitId: string;
  magasinId: string;
  boutiqueId: string;
  quantite: number;
  notes: string | null;
  date: string;
}

interface StockDoc {
  quantite: number;
  prixVente: number | null;
}

// Reprend TransfertController::index() (50 derniers transferts).
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('transferts').orderBy('date', 'desc').limit(50).get();
  const transferts = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as TransfertDoc;
      const [produit, magasin, boutique] = await Promise.all([
        resolveField('produits', data.produitId, 'nom'),
        resolveField('magasins', data.magasinId, 'nom'),
        resolveField('boutiques', data.boutiqueId, 'nom'),
      ]);
      return {
        id: doc.id,
        produit,
        magasin,
        boutique,
        quantite: data.quantite,
        date: data.date,
      };
    }),
  );

  return Response.json(transferts);
});

// Reprend TransfertController::store() : vérifie la dispo, décrémente le stock
// magasin, incrémente le stock boutique et crée le transfert, dans une seule
// transaction Firestore (équivalent du DB::transaction()+lockForUpdate() Laravel).
//
// TODO(Phase 3) : porter StockAlertNotifier une fois `notifications` conçue.
export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');
  const body = transfertSchema.parse(await request.json());

  const db = getDb();
  const stockMagasinRef = db.collection('stocksMagasin').doc(`${body.magasinId}_${body.produitId}`);
  const stockBoutiqueRef = db.collection('stocksBoutique').doc(`${body.boutiqueId}_${body.produitId}`);
  const transfertRef = db.collection('transferts').doc();

  const date = new Date().toISOString().slice(0, 10);

  await db.runTransaction(async (tx) => {
    const [stockMagasinSnap, stockBoutiqueSnap] = await Promise.all([
      tx.get(stockMagasinRef),
      tx.get(stockBoutiqueRef),
    ]);

    const stockMagasin = stockMagasinSnap.exists ? (stockMagasinSnap.data() as StockDoc) : null;
    if (!stockMagasin || stockMagasin.quantite < body.quantite) {
      throw new ApiError(422, 'Stock insuffisant dans ce magasin pour ce produit.');
    }

    tx.update(stockMagasinRef, { quantite: stockMagasin.quantite - body.quantite });

    if (stockBoutiqueSnap.exists) {
      const stockBoutique = stockBoutiqueSnap.data() as StockDoc;
      tx.update(stockBoutiqueRef, { quantite: stockBoutique.quantite + body.quantite });
    } else {
      tx.set(stockBoutiqueRef, {
        boutiqueId: body.boutiqueId,
        produitId: body.produitId,
        quantite: body.quantite,
        prixVente: stockMagasin.prixVente,
        seuilAlerte: 5,
      });
    }

    const transfert: TransfertDoc = {
      produitId: body.produitId,
      magasinId: body.magasinId,
      boutiqueId: body.boutiqueId,
      quantite: body.quantite,
      notes: body.notes ?? null,
      date,
    };
    tx.set(transfertRef, transfert);
  });

  return Response.json({ id: transfertRef.id }, { status: 201 });
});
