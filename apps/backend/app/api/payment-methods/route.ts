import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

interface PaymentMethodDoc {
  name: string;
  code: string;
  isActive: boolean;
}

// Reprend PaymentMethodController::index().
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'vendeur');

  const snapshot = await getDb().collection('paymentMethods').where('isActive', '==', true).get();
  const methods = snapshot.docs
    .map((doc) => ({ id: doc.id, data: doc.data() as PaymentMethodDoc }))
    .sort((a, b) => a.data.name.localeCompare(b.data.name))
    .map(({ id, data }) => ({ id, name: data.name, code: data.code }));

  return Response.json(methods);
});
