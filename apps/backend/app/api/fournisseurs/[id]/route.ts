import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { fournisseurSchema } from '@/lib/schemas/fournisseur';

interface FournisseurDoc {
  nom: string;
  adresse: string;
  telephone: string;
  email: string;
  contactPersonne: string;
}

function formatFournisseur(id: string, data: FournisseurDoc) {
  return {
    id,
    nom: data.nom,
    adresse: data.adresse,
    telephone: data.telephone,
    email: data.email,
    contact_personne: data.contactPersonne,
  };
}

export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const body = fournisseurSchema.parse(await request.json());
  const docRef = getDb().collection('fournisseurs').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Fournisseur introuvable.');
  }

  const duplicate = await getDb().collection('fournisseurs').where('email', '==', body.email).limit(1).get();
  if (!duplicate.empty && duplicate.docs[0].id !== id) {
    throw new ApiError(422, 'Cet email est déjà utilisé par un autre fournisseur.');
  }

  const update: FournisseurDoc = { ...body };
  await docRef.update(update);

  return Response.json(formatFournisseur(id, update));
});

export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const docRef = getDb().collection('fournisseurs').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Fournisseur introuvable.');
  }
  await docRef.delete();

  return Response.json({ message: 'Fournisseur supprimé.' });
});
