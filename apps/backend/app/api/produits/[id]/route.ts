import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { produitSchema } from '@/lib/schemas/produit';

interface ProduitDoc {
  nom: string;
  categorie: string;
  description: string;
  reference: string | null;
  prixAchat: number;
  prixVente: number;
  statut: string;
  deletedAt: string | null;
}

function formatProduit(id: string, data: ProduitDoc) {
  return {
    id,
    nom: data.nom,
    categorie: data.categorie,
    description: data.description,
    reference: data.reference,
    prix_achat: data.prixAchat,
    prix_vente: data.prixVente,
    statut: data.statut,
    deleted_at: data.deletedAt,
  };
}

export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const body = produitSchema.parse(await request.json());

  const docRef = getDb().collection('produits').doc(id);
  const existingDoc = await docRef.get();
  if (!existingDoc.exists) {
    throw new ApiError(404, 'Produit introuvable.');
  }

  if (body.reference) {
    const duplicate = await getDb().collection('produits').where('reference', '==', body.reference).limit(1).get();
    if (!duplicate.empty && duplicate.docs[0].id !== id) {
      throw new ApiError(422, 'Cette référence est déjà utilisée par un autre produit.');
    }
  }

  const update: Partial<ProduitDoc> = {
    nom: body.nom,
    categorie: body.categorie,
    description: body.description,
    reference: body.reference ?? null,
    prixAchat: body.prixAchat,
    prixVente: body.prixVente,
    statut: body.statut ?? 'actif',
  };

  await docRef.update(update);

  const merged = { ...(existingDoc.data() as ProduitDoc), ...update };
  return Response.json(formatProduit(id, merged));
});

export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const docRef = getDb().collection('produits').doc(id);
  const existingDoc = await docRef.get();
  if (!existingDoc.exists) {
    throw new ApiError(404, 'Produit introuvable.');
  }

  await docRef.update({ deletedAt: new Date().toISOString() });

  return Response.json({ message: 'Produit supprimé.' });
});
