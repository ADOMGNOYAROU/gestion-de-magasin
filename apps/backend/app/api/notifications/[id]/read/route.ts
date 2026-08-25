import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticate } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

// Reprend NotificationController::markAsRead().
export const POST = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  const authUser = await authenticate(request);
  const { id } = await params;

  const docRef = getDb().collection('notifications').doc(id);
  const doc = await docRef.get();
  if (!doc.exists || (doc.data() as { userId: string }).userId !== authUser.uid) {
    throw new ApiError(404, 'Notification introuvable.');
  }

  await docRef.update({ readAt: new Date().toISOString() });

  return Response.json({ message: 'ok' });
});
