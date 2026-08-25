import { withErrorHandling } from '@/lib/api-handler';
import { authenticate } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

// Reprend NotificationController::markAllAsRead().
export const POST = withErrorHandling(async (request) => {
  const authUser = await authenticate(request);

  const db = getDb();
  const snapshot = await db
    .collection('notifications')
    .where('userId', '==', authUser.uid)
    .where('readAt', '==', null)
    .get();

  const now = new Date().toISOString();
  const batch = db.batch();
  snapshot.docs.forEach((doc) => batch.update(doc.ref, { readAt: now }));
  await batch.commit();

  return Response.json({ message: 'ok' });
});
