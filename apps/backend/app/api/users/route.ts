import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getAdminAuth, getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { createUserSchema } from '@/lib/schemas/user';

interface UserDoc {
  name: string;
  email: string;
  role: string;
  magasinId: string | null;
  boutiqueId: string | null;
}

async function formatUser(id: string, data: UserDoc) {
  const [magasinNom, boutiqueNom] = await Promise.all([
    resolveField('magasins', data.magasinId, 'nom'),
    resolveField('boutiques', data.boutiqueId, 'nom'),
  ]);
  return {
    id,
    name: data.name,
    email: data.email,
    role: data.role,
    magasin_id: data.magasinId,
    magasin_nom: magasinNom,
    boutique_id: data.boutiqueId,
    boutique_nom: boutiqueNom,
  };
}

// Reprend UserController::index(). Accessible dès gestionnaire (pas seulement
// admin comme les mutations POST/PUT/DELETE ci-dessous) : contrairement à
// UserController (API), l'écran boutiques du contrôleur web
// (BoutiqueController::create/edit) interroge déjà tous les vendeurs sans
// restriction par magasin pour peupler le champ "vendeur" — un gestionnaire a
// donc toujours pu voir cette liste pour assigner un vendeur à une boutique.
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('users').orderBy('name').get();
  const users = await Promise.all(snapshot.docs.map((doc) => formatUser(doc.id, doc.data() as UserDoc)));

  return Response.json(users);
});

// Reprend UserController::store() : création côté Firebase Auth (email/mot de
// passe + custom claims pour le rôle et le périmètre magasin/boutique) puis
// profil Firestore avec le même uid.
export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'admin');
  const body = createUserSchema.parse(await request.json());

  const auth = getAdminAuth();
  let uid: string;
  try {
    const userRecord = await auth.createUser({
      email: body.email,
      password: body.password,
      displayName: body.name,
    });
    uid = userRecord.uid;
  } catch (error) {
    if ((error as { code?: string }).code === 'auth/email-already-exists') {
      throw new ApiError(422, 'Cet email est déjà utilisé par un autre utilisateur.');
    }
    throw error;
  }

  await auth.setCustomUserClaims(uid, {
    role: body.role,
    magasinId: body.magasinId ?? null,
    boutiqueId: body.boutiqueId ?? null,
  });

  const doc: UserDoc = {
    name: body.name,
    email: body.email,
    role: body.role,
    magasinId: body.magasinId ?? null,
    boutiqueId: body.boutiqueId ?? null,
  };
  await getDb().collection('users').doc(uid).set(doc);

  return Response.json(await formatUser(uid, doc), { status: 201 });
});
