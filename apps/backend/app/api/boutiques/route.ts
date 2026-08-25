import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { boutiqueSchema } from '@/lib/schemas/boutique';

interface BoutiqueDoc {
  nom: string;
  adresse: string;
  telephone: string;
  email: string | null;
  magasinId: string;
  vendeurId: string | null;
}

async function formatBoutique(id: string, data: BoutiqueDoc) {
  const [magasinNom, vendeurNom] = await Promise.all([
    resolveField('magasins', data.magasinId, 'nom'),
    resolveField('users', data.vendeurId, 'name'),
  ]);
  return {
    id,
    nom: data.nom,
    adresse: data.adresse,
    telephone: data.telephone,
    email: data.email,
    magasin_id: data.magasinId,
    magasin_nom: magasinNom,
    vendeur_id: data.vendeurId,
    vendeur: vendeurNom,
  };
}

async function assertEmailUnique(email: string | null | undefined, ignoreId?: string) {
  if (!email) return;
  const existing = await getDb().collection('boutiques').where('email', '==', email).limit(1).get();
  if (!existing.empty && existing.docs[0].id !== ignoreId) {
    throw new ApiError(422, 'Cet email est déjà utilisé par une autre boutique.');
  }
}

// CRUD réservé aux gestionnaires et admins (cf. routes/web.php: middleware('gestionnaire')).
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('boutiques').orderBy('nom').get();
  const boutiques = await Promise.all(
    snapshot.docs.map((doc) => formatBoutique(doc.id, doc.data() as BoutiqueDoc)),
  );

  return Response.json(boutiques);
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const body = boutiqueSchema.parse(await request.json());
  await assertEmailUnique(body.email);

  const doc: BoutiqueDoc = {
    nom: body.nom,
    adresse: body.adresse,
    telephone: body.telephone,
    email: body.email ?? null,
    magasinId: body.magasinId,
    vendeurId: body.vendeurId ?? null,
  };

  const ref = await getDb().collection('boutiques').add(doc);

  return Response.json(await formatBoutique(ref.id, doc), { status: 201 });
});
