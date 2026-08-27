import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';

interface CreditDoc {
  venteId: string;
  clientId: string;
  totalAmount: number;
  remainingBalance: number;
  status: 'active' | 'paid';
  createdAt: string;
}

interface VenteDoc {
  boutiqueId: string;
  numeroTicket: string;
  montantTotal: number;
}

interface CreditPaymentDoc {
  amount: number;
  paymentDate: string;
  notes: string | null;
  createdAt: string;
}

// Reprend CreditController::show() (centro).
export const GET = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'vendeur');
  const { id } = await params;

  const db = getDb();
  const docRef = db.collection('credits').doc(id);
  const doc = await docRef.get();
  if (!doc.exists) {
    throw new ApiError(404, 'Crédit introuvable.');
  }
  const data = doc.data() as CreditDoc;

  const [venteDoc, paymentsSnap, clientNom] = await Promise.all([
    db.collection('ventes').doc(data.venteId).get(),
    docRef.collection('paiements').orderBy('paymentDate', 'desc').get(),
    resolveField('clients', data.clientId, 'nom'),
  ]);
  const vente = venteDoc.exists ? (venteDoc.data() as VenteDoc) : null;
  const boutiqueNom = vente ? await resolveField('boutiques', vente.boutiqueId, 'nom') : null;

  const paiements = paymentsSnap.docs.map((d) => {
    const p = d.data() as CreditPaymentDoc;
    return { id: d.id, amount: p.amount, payment_date: p.paymentDate, notes: p.notes };
  });

  return Response.json({
    id: doc.id,
    client: clientNom,
    client_id: data.clientId,
    boutique: boutiqueNom,
    numero_ticket: vente?.numeroTicket ?? null,
    total_amount: data.totalAmount,
    remaining_balance: data.remainingBalance,
    status: data.status,
    created_at: data.createdAt,
    paiements,
  });
});
