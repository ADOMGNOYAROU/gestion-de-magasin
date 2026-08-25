import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

// Reprend TransfertController::stockDisponible().
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const { searchParams } = new URL(request.url);
  const produitId = searchParams.get('produit_id');
  const magasinId = searchParams.get('magasin_id');

  if (!produitId || !magasinId) {
    return Response.json({ quantite: 0 });
  }

  const doc = await getDb().collection('stocksMagasin').doc(`${magasinId}_${produitId}`).get();
  const quantite = doc.exists ? (doc.data() as { quantite: number }).quantite : 0;

  return Response.json({ quantite });
});
