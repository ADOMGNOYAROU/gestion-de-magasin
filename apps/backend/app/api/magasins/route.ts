import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
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

// Le CRUD magasins est réservé aux admins (cf. routes/web.php: middleware('admin')).
// Ne couvre pas encore le scoping fin par magasin (Gate::manage-magasin dans
// PERMISSIONS_GUIDE.md), qui ne s'applique qu'aux vues gestionnaire à construire plus tard.
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'admin');

  const snapshot = await getDb().collection('magasins').orderBy('nom').get();
  const magasins = await Promise.all(
    snapshot.docs.map((doc) => formatMagasin(doc.id, doc.data() as MagasinDoc)),
  );

  return Response.json(magasins);
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'admin');

  const body = magasinSchema.parse(await request.json());
  const doc: MagasinDoc = {
    nom: body.nom,
    localisation: body.localisation,
    responsableId: body.responsableId ?? null,
  };

  const ref = await getDb().collection('magasins').add(doc);

  return Response.json(await formatMagasin(ref.id, doc), { status: 201 });
});
