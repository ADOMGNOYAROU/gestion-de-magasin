import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { CashRegisterSessionDoc, formatCashRegisterSession } from '@/lib/format/cash-register-session';

// Reprend CashRegisterSessionController::current().
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'vendeur');

  const snapshot = await getDb()
    .collection('cashRegisterSessions')
    .where('vendeurId', '==', authUser.uid)
    .where('status', '==', 'ouverte')
    .orderBy('dateOuverture', 'desc')
    .limit(1)
    .get();

  if (snapshot.empty) {
    return Response.json(null);
  }

  const doc = snapshot.docs[0];
  return Response.json(formatCashRegisterSession(doc.id, doc.data() as CashRegisterSessionDoc));
});
