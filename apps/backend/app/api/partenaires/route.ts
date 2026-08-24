import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { partenaireSchema } from '@/lib/schemas/partenaire';

interface PartenaireDoc {
  nom: string;
  adresse: string;
  telephone: string;
  email: string;
  typePartenariat: string;
}

function formatPartenaire(id: string, data: PartenaireDoc) {
  return {
    id,
    nom: data.nom,
    adresse: data.adresse,
    telephone: data.telephone,
    email: data.email,
    type_partenariat: data.typePartenariat,
  };
}

export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('partenaires').orderBy('nom').get();
  return Response.json(snapshot.docs.map((doc) => formatPartenaire(doc.id, doc.data() as PartenaireDoc)));
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const body = partenaireSchema.parse(await request.json());

  const existing = await getDb().collection('partenaires').where('email', '==', body.email).limit(1).get();
  if (!existing.empty) {
    throw new ApiError(422, 'Cet email est déjà utilisé par un autre partenaire.');
  }

  const doc: PartenaireDoc = { ...body };
  const ref = await getDb().collection('partenaires').add(doc);

  return Response.json(formatPartenaire(ref.id, doc), { status: 201 });
});
