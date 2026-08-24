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

export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const { searchParams } = new URL(request.url);
  const withTrashed = searchParams.get('with_trashed') === 'true' || searchParams.get('with_trashed') === '1';
  const search = searchParams.get('search')?.toLowerCase() ?? null;
  const categorie = searchParams.get('categorie');

  // Catalogue de la taille d'une PME multi-boutiques : filtrage en mémoire plutôt que
  // des index Firestore composites, plus simple à faire évoluer tant que le volume reste modeste.
  const snapshot = await getDb().collection('produits').get();

  let produits = snapshot.docs.map((doc) => ({ id: doc.id, data: doc.data() as ProduitDoc }));

  if (!withTrashed) {
    produits = produits.filter((p) => !p.data.deletedAt);
  }
  if (categorie) {
    produits = produits.filter((p) => p.data.categorie === categorie);
  }
  if (search) {
    produits = produits.filter(
      (p) => p.data.nom.toLowerCase().includes(search) || p.data.reference?.toLowerCase().includes(search),
    );
  }

  produits.sort((a, b) => a.data.nom.localeCompare(b.data.nom));

  return Response.json(produits.map((p) => formatProduit(p.id, p.data)));
});

export const POST = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const body = produitSchema.parse(await request.json());

  if (body.reference) {
    const existing = await getDb().collection('produits').where('reference', '==', body.reference).limit(1).get();
    if (!existing.empty) {
      throw new ApiError(422, 'Cette référence est déjà utilisée par un autre produit.');
    }
  }

  const doc: ProduitDoc = {
    nom: body.nom,
    categorie: body.categorie,
    description: body.description,
    reference: body.reference ?? null,
    prixAchat: body.prixAchat,
    prixVente: body.prixVente,
    statut: body.statut ?? 'actif',
    deletedAt: null,
  };

  const ref = await getDb().collection('produits').add(doc);

  return Response.json(formatProduit(ref.id, doc), { status: 201 });
});
