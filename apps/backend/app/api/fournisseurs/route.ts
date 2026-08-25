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

export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('fournisseurs').orderBy('nom').get();
  return Response.json(snapshot.docs.map((doc) => formatFournisseur(doc.id, doc.data() as FournisseurDoc)));
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const body = fournisseurSchema.parse(await request.json());

  const existing = await getDb().collection('fournisseurs').where('email', '==', body.email).limit(1).get();
  if (!existing.empty) {
    throw new ApiError(422, 'Cet email est déjà utilisé par un autre fournisseur.');
  }

  const doc: FournisseurDoc = { ...body };
  const ref = await getDb().collection('fournisseurs').add(doc);

  return Response.json(formatFournisseur(ref.id, doc), { status: 201 });
});
