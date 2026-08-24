import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { CashRegisterSessionDoc, formatCashRegisterSession } from '@/lib/format/cash-register-session';
import { closeSessionSchema } from '@/lib/schemas/cash-register-session';

interface VenteDoc {
  sessionCaisseId: string;
  montantTotal: number;
}

// Reprend CashRegisterSessionController::close() + CashRegisterSession::fermer().
export const POST = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  const authUser = await authenticateWithRole(request, 'vendeur');
  const { id } = await params;
  const body = closeSessionSchema.parse(await request.json());

  const db = getDb();
  const sessionRef = db.collection('cashRegisterSessions').doc(id);
  const sessionDoc = await sessionRef.get();
  if (!sessionDoc.exists) {
    throw new ApiError(404, 'Session de caisse introuvable.');
  }
  const session = sessionDoc.data() as CashRegisterSessionDoc;

  if (session.vendeurId !== authUser.uid) {
    throw new ApiError(403, 'Accès interdit.');
  }

  const ventesSnapshot = await db.collection('ventes').where('sessionCaisseId', '==', id).get();
  const totalVentes = ventesSnapshot.docs.reduce(
    (sum, doc) => sum + ((doc.data() as VenteDoc).montantTotal ?? 0),
    0,
  );
  const montantTheorique = session.montantInitial + totalVentes;

  const update: Partial<CashRegisterSessionDoc> = {
    montantTheorique,
    montantFinal: body.montantFinal,
    ecart: body.montantFinal - montantTheorique,
    dateFermeture: new Date().toISOString(),
    status: 'fermee',
    notes: body.notes ?? session.notes ?? null,
  };
  await sessionRef.update(update);

  return Response.json(formatCashRegisterSession(id, { ...session, ...update }));
});
