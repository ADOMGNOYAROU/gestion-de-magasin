import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getAdminAuth, getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { updateUserSchema } from '@/lib/schemas/user';

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

// Reprend UserController::update().
export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'admin');
  const { id } = await params;
  const body = updateUserSchema.parse(await request.json());

  const docRef = getDb().collection('users').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Utilisateur introuvable.');
  }

  const auth = getAdminAuth();
  try {
    await auth.updateUser(id, {
      email: body.email,
      displayName: body.name,
      ...(body.password ? { password: body.password } : {}),
    });
  } catch (error) {
    if ((error as { code?: string }).code === 'auth/email-already-exists') {
      throw new ApiError(422, 'Cet email est déjà utilisé par un autre utilisateur.');
    }
    throw error;
  }

  await auth.setCustomUserClaims(id, {
    role: body.role,
    magasinId: body.magasinId ?? null,
    boutiqueId: body.boutiqueId ?? null,
  });

  const update: UserDoc = {
    name: body.name,
    email: body.email,
    role: body.role,
    magasinId: body.magasinId ?? null,
    boutiqueId: body.boutiqueId ?? null,
  };
  await docRef.update(update);

  return Response.json(await formatUser(id, update));
});

// Reprend UserController::destroy().
export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  const authUser = await authenticateWithRole(request, 'admin');
  const { id } = await params;

  if (id === authUser.uid) {
    throw new ApiError(422, 'Vous ne pouvez pas supprimer votre propre compte.');
  }

  const docRef = getDb().collection('users').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Utilisateur introuvable.');
  }

  await Promise.all([getAdminAuth().deleteUser(id), docRef.delete()]);

  return Response.json({ message: 'Utilisateur supprimé.' });
});
