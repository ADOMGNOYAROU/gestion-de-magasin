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

export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const body = boutiqueSchema.parse(await request.json());
  const docRef = getDb().collection('boutiques').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Boutique introuvable.');
  }
  await assertEmailUnique(body.email, id);

  const update: BoutiqueDoc = {
    nom: body.nom,
    adresse: body.adresse,
    telephone: body.telephone,
    email: body.email ?? null,
    magasinId: body.magasinId,
    vendeurId: body.vendeurId ?? null,
  };
  await docRef.update(update);

  return Response.json(await formatBoutique(id, update));
});

export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const docRef = getDb().collection('boutiques').doc(id);
  if (!(await docRef.get()).exists) {
    throw new ApiError(404, 'Boutique introuvable.');
  }
  await docRef.delete();

  return Response.json({ message: 'Boutique supprimée.' });
});
