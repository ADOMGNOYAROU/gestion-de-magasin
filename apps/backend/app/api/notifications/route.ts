import { withErrorHandling } from '@/lib/api-handler';
import { authenticate } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

interface NotificationDoc {
  userId: string;
  data: Record<string, unknown>;
  readAt: string | null;
  createdAt: string;
}

// Reprend NotificationController::index().
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticate(request);

  const snapshot = await getDb()
    .collection('notifications')
    .where('userId', '==', authUser.uid)
    .orderBy('createdAt', 'desc')
    .limit(50)
    .get();

  const notifications = snapshot.docs.map((doc) => {
    const data = doc.data() as NotificationDoc;
    return {
      id: doc.id,
      title: (data.data['title'] as string | undefined) ?? null,
      message: (data.data['message'] as string | undefined) ?? null,
      data: data.data,
      read: data.readAt !== null,
      created_at: data.createdAt,
    };
  });

  return Response.json(notifications);
});
