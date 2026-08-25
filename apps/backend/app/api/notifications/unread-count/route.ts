import { withErrorHandling } from '@/lib/api-handler';
import { authenticate } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

// Reprend NotificationController::unreadCount().
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticate(request);

  const snapshot = await getDb()
    .collection('notifications')
    .where('userId', '==', authUser.uid)
    .where('readAt', '==', null)
    .count()
    .get();

  return Response.json({ count: snapshot.data().count });
});
