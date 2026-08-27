import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { creditPaymentSchema } from '@/lib/schemas/credit-payment';

interface CreditDoc {
  venteId: string;
  clientId: string;
  totalAmount: number;
  remainingBalance: number;
  status: 'active' | 'paid';
}

interface VenteDoc {
  boutiqueId: string;
}

interface VenteLigneDoc {
  produitId: string;
  quantite: number;
}

interface StockBoutiqueDoc {
  quantite: number;
}

// Reprend CreditController::storePayment() (centro) : enregistre le paiement,
// met à jour le solde du crédit, et décrémente le stock boutique
// proportionnellement au paiement (la marchandise n'est réellement "sortie"
// qu'au fur et à mesure qu'elle est payée — voir la note dans
// app/api/ventes/route.ts sur la réconciliation avec la vente initiale).
export const POST = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'vendeur');
  const { id } = await params;
  const body = creditPaymentSchema.parse(await request.json());

  const db = getDb();
  const creditRef = db.collection('credits').doc(id);

  const result = await db.runTransaction(async (tx) => {
    const creditDoc = await tx.get(creditRef);
    if (!creditDoc.exists) {
      throw new ApiError(404, 'Crédit introuvable.');
    }
    const credit = creditDoc.data() as CreditDoc;
    if (credit.status === 'paid') {
      throw new ApiError(422, 'Ce crédit est déjà payé.');
    }
    if (body.amount > credit.remainingBalance) {
      throw new ApiError(422, 'Le montant ne peut pas dépasser le solde restant.');
    }

    const venteRef = db.collection('ventes').doc(credit.venteId);
    const venteDoc = await tx.get(venteRef);
    const ligneSnaps = await tx.get(venteRef.collection('produits'));
    const vente = venteDoc.exists ? (venteDoc.data() as VenteDoc) : null;

    const lignes = ligneSnaps.docs.map((d) => d.data() as VenteLigneDoc);
    const stockRefs = vente
      ? lignes.map((l) => db.collection('stocksBoutique').doc(`${vente.boutiqueId}_${l.produitId}`))
      : [];
    const stockSnaps = await Promise.all(stockRefs.map((ref) => tx.get(ref)));

    // --- Écritures ---
    const paymentRef = creditRef.collection('paiements').doc();
    tx.set(paymentRef, {
      amount: body.amount,
      paymentDate: body.paymentDate,
      notes: body.notes ?? null,
      createdAt: new Date().toISOString(),
    });

    const remainingBalance = credit.remainingBalance - body.amount;
    tx.update(creditRef, {
      remainingBalance: Math.max(remainingBalance, 0),
      status: remainingBalance <= 0 ? 'paid' : 'active',
    });

    stockSnaps.forEach((stockSnap, i) => {
      if (!stockSnap.exists) return;
      const decrease = Math.round((body.amount / credit.totalAmount) * lignes[i].quantite);
      if (decrease <= 0) return;
      const stock = stockSnap.data() as StockBoutiqueDoc;
      tx.update(stockRefs[i], { quantite: Math.max(stock.quantite - decrease, 0) });
    });

    return { remaining_balance: Math.max(remainingBalance, 0), status: remainingBalance <= 0 ? 'paid' : 'active' };
  });

  return Response.json(result, { status: 201 });
});
