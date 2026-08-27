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

// Reprend ClientController (centro) : accessible dès vendeur, comme dans web.php
// (Route::resource('clients', ...)->middleware('vendeur')) — un vendeur doit
// pouvoir créer une fiche client au moment de la vente.
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'vendeur');

  const snapshot = await getDb().collection('clients').orderBy('nom').get();
  return Response.json(snapshot.docs.map((doc) => formatClient(doc.id, doc.data() as ClientDoc)));
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'vendeur');
  const body = clientSchema.parse(await request.json());

  if (body.email) {
    const existing = await getDb().collection('clients').where('email', '==', body.email).limit(1).get();
    if (!existing.empty) {
      throw new ApiError(422, 'Cet email est déjà utilisé par un autre client.');
    }
  }

  const doc: ClientDoc = {
    nom: body.nom,
    prenom: body.prenom ?? null,
    email: body.email ?? null,
    telephone: body.telephone ?? null,
    adresse: body.adresse ?? null,
  };
  const ref = await getDb().collection('clients').add(doc);

  return Response.json(formatClient(ref.id, doc), { status: 201 });
});
