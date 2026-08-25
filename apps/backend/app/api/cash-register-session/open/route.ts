import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { CashRegisterSessionDoc, formatCashRegisterSession } from '@/lib/format/cash-register-session';
import { openSessionSchema } from '@/lib/schemas/cash-register-session';

// Reprend CashRegisterSessionController::open().
export const POST = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'vendeur');
  const body = openSessionSchema.parse(await request.json());

  const boutiqueId = body.boutiqueId ?? authUser.boutiqueId;
  if (!boutiqueId) {
    throw new ApiError(422, 'Aucune boutique associée à cet utilisateur.');
  }

  const db = getDb();
  const existing = await db
    .collection('cashRegisterSessions')
    .where('vendeurId', '==', authUser.uid)
    .where('status', '==', 'ouverte')
    .limit(1)
    .get();

  if (!existing.empty) {
    throw new ApiError(422, 'Une session de caisse est déjà ouverte.');
  }

  const doc: CashRegisterSessionDoc = {
    vendeurId: authUser.uid,
    boutiqueId,
    montantInitial: body.montantInitial,
    montantFinal: null,
    montantTheorique: body.montantInitial,
    ecart: null,
    status: 'ouverte',
    dateOuverture: new Date().toISOString(),
    dateFermeture: null,
    notes: null,
  };

  const ref = await db.collection('cashRegisterSessions').add(doc);

  return Response.json(formatCashRegisterSession(ref.id, doc), { status: 201 });
});
