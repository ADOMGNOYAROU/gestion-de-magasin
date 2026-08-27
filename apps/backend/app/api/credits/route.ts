import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
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
}

// Reprend CreditController::index() (centro).
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'vendeur');

  const snapshot = await getDb().collection('credits').orderBy('createdAt', 'desc').limit(100).get();

  const credits = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as CreditDoc;
      const venteDoc = await getDb().collection('ventes').doc(data.venteId).get();
      const vente = venteDoc.exists ? (venteDoc.data() as VenteDoc) : null;
      const [clientNom, boutiqueNom] = await Promise.all([
        resolveField('clients', data.clientId, 'nom'),
        vente ? resolveField('boutiques', vente.boutiqueId, 'nom') : Promise.resolve(null),
      ]);
      return {
        id: doc.id,
        client: clientNom,
        client_id: data.clientId,
        boutique: boutiqueNom,
        numero_ticket: vente?.numeroTicket ?? null,
        total_amount: data.totalAmount,
        remaining_balance: data.remainingBalance,
        status: data.status,
        created_at: data.createdAt,
      };
    }),
  );

  return Response.json(credits);
});
