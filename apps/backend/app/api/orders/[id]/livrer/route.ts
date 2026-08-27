import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

interface OrderDoc {
  magasinId: string;
  status: 'en_cours' | 'livree' | 'annulee';
}

interface OrderItemDoc {
  produitId: string;
  quantite: number;
}

interface StockMagasinDoc {
  magasinId: string;
  produitId: string;
  quantite: number;
  prixVente: number | null;
  seuilAlerte: number;
}

// Reprend OrderController::livrer() : incrémente le stock du magasin de la
// commande pour chaque ligne, dans une seule transaction Firestore.
export const POST = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const db = getDb();
  const orderRef = db.collection('orders').doc(id);

  await db.runTransaction(async (tx) => {
    const orderDoc = await tx.get(orderRef);
    if (!orderDoc.exists) {
      throw new ApiError(404, 'Commande introuvable.');
    }
    const order = orderDoc.data() as OrderDoc;
    if (order.status !== 'en_cours') {
      throw new ApiError(422, "La commande n'est pas en cours.");
    }

    const itemsSnap = await tx.get(orderRef.collection('items'));
    const items = itemsSnap.docs.map((d) => d.data() as OrderItemDoc);
    const stockRefs = items.map((item) =>
      db.collection('stocksMagasin').doc(`${order.magasinId}_${item.produitId}`),
    );
    const stockSnaps = await Promise.all(stockRefs.map((ref) => tx.get(ref)));

    stockSnaps.forEach((stockSnap, i) => {
      if (stockSnap.exists) {
        const stock = stockSnap.data() as StockMagasinDoc;
        tx.update(stockRefs[i], { quantite: stock.quantite + items[i].quantite });
      } else {
        const newStock: StockMagasinDoc = {
          magasinId: order.magasinId,
          produitId: items[i].produitId,
          quantite: items[i].quantite,
          prixVente: null,
          seuilAlerte: 10,
        };
        tx.set(stockRefs[i], newStock);
      }
    });

    tx.update(orderRef, { status: 'livree' });
  });

  return Response.json({ message: 'Commande marquée comme livrée et stock mis à jour.' });
});
