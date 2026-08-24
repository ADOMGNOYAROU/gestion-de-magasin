import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';

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

export const POST = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const docRef = getDb().collection('produits').doc(id);
  const existingDoc = await docRef.get();
  if (!existingDoc.exists) {
    throw new ApiError(404, 'Produit introuvable.');
  }

  await docRef.update({ deletedAt: null });

  const merged = { ...(existingDoc.data() as ProduitDoc), deletedAt: null };
  return Response.json(formatProduit(id, merged));
});
