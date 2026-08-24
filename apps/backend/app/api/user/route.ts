import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticate } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

interface UserDoc {
  name: string;
  email: string;
  role: string;
  magasinId: string | null;
  boutiqueId: string | null;
}

// Équivalent de AuthController::me() — l'inscription/connexion elles-mêmes sont
// gérées côté client par le SDK Firebase Auth, pas par cette API.
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticate(request);

  const userDoc = await getDb().collection('users').doc(authUser.uid).get();
  if (!userDoc.exists) {
    throw new ApiError(404, 'Profil utilisateur introuvable.');
  }
  const data = userDoc.data() as UserDoc;

  const [magasinDoc, boutiqueDoc] = await Promise.all([
    data.magasinId ? getDb().collection('magasins').doc(data.magasinId).get() : null,
    data.boutiqueId ? getDb().collection('boutiques').doc(data.boutiqueId).get() : null,
  ]);

  return Response.json({
    id: authUser.uid,
    name: data.name,
    email: data.email,
    role: data.role,
    magasin_id: data.magasinId,
    boutique_id: data.boutiqueId,
    magasin_nom: magasinDoc?.exists ? (magasinDoc.data() as { nom: string }).nom : null,
    boutique_nom: boutiqueDoc?.exists ? (boutiqueDoc.data() as { nom: string }).nom : null,
  });
});
