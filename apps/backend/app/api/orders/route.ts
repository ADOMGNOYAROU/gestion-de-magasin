import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
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

async function formatOrder(id: string, data: OrderDoc) {
  const [fournisseurNom, userName] = await Promise.all([
    resolveField('fournisseurs', data.fournisseurId, 'nom'),
    resolveField('users', data.userId, 'name'),
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
  };
}

function generateNumeroPrefix(): string {
  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, '0');
  const d = String(now.getDate()).padStart(2, '0');
  return `CMD-${y}${m}${d}-`;
}

// Reprend OrderController::index() (recherche + filtre statut) et ::store().
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const { searchParams } = new URL(request.url);
  const search = searchParams.get('search')?.toLowerCase() ?? null;
  const status = searchParams.get('status');

  const snapshot = await getDb().collection('orders').orderBy('dateCommande', 'desc').limit(100).get();
  let orders = snapshot.docs.map((doc) => ({ id: doc.id, data: doc.data() as OrderDoc }));

  if (status) {
    orders = orders.filter((o) => o.data.status === status);
  }
  if (search) {
    orders = orders.filter((o) => o.data.numeroCommande.toLowerCase().includes(search));
  }

  return Response.json(await Promise.all(orders.map((o) => formatOrder(o.id, o.data))));
});

export const POST = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'gestionnaire');
  const body = orderSchema.parse(await request.json());

  const db = getDb();
  const prefix = generateNumeroPrefix();
  const orderRef = db.collection('orders').doc();

  const numeroCommande = await db.runTransaction(async (tx) => {
    const numSnap = await tx.get(
      db
        .collection('orders')
        .where('numeroCommande', '>=', prefix)
        .where('numeroCommande', '<', prefix + '')
        .orderBy('numeroCommande', 'desc')
        .limit(1),
    );
    let sequence = 1;
    if (!numSnap.empty) {
      const parts = (numSnap.docs[0].data() as OrderDoc).numeroCommande.split('-');
      if (parts.length >= 3) sequence = parseInt(parts[2], 10) + 1;
    }
    const numero = `${prefix}${String(sequence).padStart(4, '0')}`;

    const lignes = body.lignes.map((l) => ({
      produitId: l.produitId,
      quantite: l.quantite,
      prixUnitaire: l.prixUnitaire,
      sousTotal: l.prixUnitaire * l.quantite,
    }));
    const montantTotal = lignes.reduce((sum, l) => sum + l.sousTotal, 0);

    const order: OrderDoc = {
      fournisseurId: body.fournisseurId,
      magasinId: body.magasinId,
      userId: authUser.uid,
      numeroCommande: numero,
      dateCommande: new Date().toISOString().slice(0, 10),
      dateLivraisonPrevue: body.dateLivraisonPrevue ?? null,
      status: 'en_cours',
      montantTotal,
      notes: body.notes ?? null,
    };
    tx.set(orderRef, order);
    lignes.forEach((ligne) => tx.set(orderRef.collection('items').doc(), ligne as OrderItemDoc));

    return numero;
  });

  return Response.json({ id: orderRef.id, numero_commande: numeroCommande }, { status: 201 });
});
