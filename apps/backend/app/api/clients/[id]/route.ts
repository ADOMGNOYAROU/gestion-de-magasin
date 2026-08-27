import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { clientSchema } from '@/lib/schemas/client';

interface ClientDoc {
  nom: string;
  prenom: string | null;
  email: string | null;
  telephone: string | null;
  adresse: string | null;
}

function formatClient(id: string, data: ClientDoc) {
  return {
    id,
    nom: data.nom,
    prenom: data.prenom,
    email: data.email,
    telephone: data.telephone,
    adresse: data.adresse,
  };
}

export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'vendeur');
  const { id } = await params;
  const body = clientSchema.parse(await request.json());

  const docRef = getDb().collection('clients').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Client introuvable.');
  }

  if (body.email) {
    const duplicate = await getDb().collection('clients').where('email', '==', body.email).limit(1).get();
    if (!duplicate.empty && duplicate.docs[0].id !== id) {
      throw new ApiError(422, 'Cet email est déjà utilisé par un autre client.');
    }
  }

  const update: ClientDoc = {
    nom: body.nom,
    prenom: body.prenom ?? null,
    email: body.email ?? null,
    telephone: body.telephone ?? null,
    adresse: body.adresse ?? null,
  };
  await docRef.update(update);

  return Response.json(formatClient(id, update));
});

export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'vendeur');
  const { id } = await params;

  const docRef = getDb().collection('clients').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Client introuvable.');
  }
  await docRef.delete();

  return Response.json({ message: 'Client supprimé.' });
});
