import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { magasinSchema } from '@/lib/schemas/magasin';

interface MagasinDoc {
  nom: string;
  localisation: string;
  responsableId: string | null;
}

async function formatMagasin(id: string, data: MagasinDoc) {
  return {
    id,
    nom: data.nom,
    localisation: data.localisation,
    responsable_id: data.responsableId,
    responsable: await resolveField('users', data.responsableId, 'name'),
  };
}

export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'admin');
  const { id } = await params;

  const body = magasinSchema.parse(await request.json());
  const docRef = getDb().collection('magasins').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Magasin introuvable.');
  }

  const update: MagasinDoc = {
    nom: body.nom,
    localisation: body.localisation,
    responsableId: body.responsableId ?? null,
  };
  await docRef.update(update);

  return Response.json(await formatMagasin(id, update));
});

export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'admin');
  const { id } = await params;

  const docRef = getDb().collection('magasins').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Magasin introuvable.');
  }
  await docRef.delete();

  return Response.json({ message: 'Magasin supprimé.' });
});
