import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { orderSchema } from '@/lib/schemas/order';

interface OrderDoc {
  fournisseurId: string;
  magasinId: string;
  userId: string;
  numeroCommande: string;
  dateCommande: string;
  dateLivraisonPrevue: string | null;
  status: 'en_cours' | 'livree' | 'annulee';
  montantTotal: number;
  notes: string | null;
}

interface OrderItemDoc {
  produitId: string;
  quantite: number;
  prixUnitaire: number;
  sousTotal: number;
}

async function formatOrderDetail(id: string, data: OrderDoc, items: { id: string; data: OrderItemDoc }[]) {
  const [fournisseurNom, userName, lignes] = await Promise.all([
    resolveField('fournisseurs', data.fournisseurId, 'nom'),
    resolveField('users', data.userId, 'name'),
    Promise.all(
      items.map(async (item) => ({
        produit: await resolveField('produits', item.data.produitId, 'nom'),
        produit_id: item.data.produitId,
        quantite: item.data.quantite,
        prix_unitaire: item.data.prixUnitaire,
        sous_total: item.data.sousTotal,
      })),
    ),
  ]);
  return {
    id,
    fournisseur: fournisseurNom,
    fournisseur_id: data.fournisseurId,
    magasin_id: data.magasinId,
    user: userName,
    numero_commande: data.numeroCommande,
    date_commande: data.dateCommande,
    date_livraison_prevue: data.dateLivraisonPrevue,
    status: data.status,
    montant_total: data.montantTotal,
    notes: data.notes,
    lignes,
  };
}

export const GET = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const db = getDb();
  const docRef = db.collection('orders').doc(id);
  const [doc, itemsSnap] = await Promise.all([docRef.get(), docRef.collection('items').get()]);
  if (!doc.exists) {
    throw new ApiError(404, 'Commande introuvable.');
  }

  const items = itemsSnap.docs.map((d) => ({ id: d.id, data: d.data() as OrderItemDoc }));
  return Response.json(await formatOrderDetail(id, doc.data() as OrderDoc, items));
});

// Reprend OrderController::update() : seules les commandes en_cours sont modifiables.
export const PUT = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;
  const body = orderSchema.parse(await request.json());

  const db = getDb();
  const docRef = db.collection('orders').doc(id);
  const doc = await docRef.get();
  if (!doc.exists) {
    throw new ApiError(404, 'Commande introuvable.');
  }
  const existing = doc.data() as OrderDoc;
  if (existing.status !== 'en_cours') {
    throw new ApiError(422, 'Seules les commandes en cours peuvent être modifiées.');
  }

  const lignes = body.lignes.map((l) => ({
    produitId: l.produitId,
    quantite: l.quantite,
    prixUnitaire: l.prixUnitaire,
    sousTotal: l.prixUnitaire * l.quantite,
  }));
  const montantTotal = lignes.reduce((sum, l) => sum + l.sousTotal, 0);

  const itemsSnap = await docRef.collection('items').get();
  const batch = db.batch();
  itemsSnap.docs.forEach((d) => batch.delete(d.ref));
  lignes.forEach((ligne) => batch.set(docRef.collection('items').doc(), ligne as OrderItemDoc));
  batch.update(docRef, {
    fournisseurId: body.fournisseurId,
    magasinId: body.magasinId,
    dateLivraisonPrevue: body.dateLivraisonPrevue ?? null,
    notes: body.notes ?? null,
    montantTotal,
  });
  await batch.commit();

  return Response.json({ id, montant_total: montantTotal });
});

// Reprend OrderController::destroy() : annule (statut) plutôt que supprime,
// et uniquement si la commande est encore en_cours.
export const DELETE = withErrorHandling(async (request, { params }: { params: Promise<{ id: string }> }) => {
  await authenticateWithRole(request, 'gestionnaire');
  const { id } = await params;

  const docRef = getDb().collection('orders').doc(id);
  const doc = await docRef.get();
  if (!doc.exists) {
    throw new ApiError(404, 'Commande introuvable.');
  }
  if ((doc.data() as OrderDoc).status !== 'en_cours') {
    throw new ApiError(422, 'Seules les commandes en cours peuvent être annulées.');
  }

  await docRef.update({ status: 'annulee' });
  return Response.json({ message: 'Commande annulée.' });
});
