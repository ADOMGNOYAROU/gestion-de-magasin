import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';

interface StockDoc {
  produitId: string;
  magasinId?: string;
  boutiqueId?: string;
  quantite: number;
  seuilAlerte: number;
}

interface VenteDoc {
  boutiqueId: string;
  montantTotal: number;
  dateVente: string;
}

// Reprend DashboardController::index(). Firestore n'a pas d'équivalent à
// whereColumn('quantite', '<=', 'seuil_alerte') : on filtre en mémoire, comme
// pour /api/stock-magasins et /api/stock-boutiques (même volumétrie attendue).
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'vendeur');

  const db = getDb();
  const today = new Date().toISOString().slice(0, 10);

  const [produitsActifsSnap, stocksMagasinSnap, stocksBoutiqueSnap, ventesSnap] = await Promise.all([
    // deletedAt == null est nécessaire car Firestore n'a pas de soft-delete global
    // scope comme Eloquent : un produit supprimé garde son statut 'actif' tel quel.
    db.collection('produits').where('statut', '==', 'actif').where('deletedAt', '==', null).count().get(),
    db.collection('stocksMagasin').get(),
    db.collection('stocksBoutique').get(),
    db.collection('ventes').where('dateVente', '==', today).get(),
  ]);

  const stocksMagasinCritiques = stocksMagasinSnap.docs
    .map((doc) => doc.data() as StockDoc)
    .filter((s) => s.quantite <= s.seuilAlerte);
  const stocksBoutiqueCritiques = stocksBoutiqueSnap.docs
    .map((doc) => doc.data() as StockDoc)
    .filter((s) => s.quantite <= s.seuilAlerte);

  let ventes = ventesSnap.docs.map((doc) => doc.data() as VenteDoc);
  if (authUser.role === 'vendeur' && authUser.boutiqueId) {
    ventes = ventes.filter((v) => v.boutiqueId === authUser.boutiqueId);
  }

  const alertesMagasin = await Promise.all(
    stocksMagasinCritiques.slice(0, 10).map(async (s) => ({
      produit: await resolveField('produits', s.produitId, 'nom'),
      site: await resolveField('magasins', s.magasinId ?? null, 'nom'),
      type: 'magasin',
      quantite: s.quantite,
      seuil_alerte: s.seuilAlerte,
    })),
  );
  const alertesBoutique = await Promise.all(
    stocksBoutiqueCritiques.slice(0, 10).map(async (s) => ({
      produit: await resolveField('produits', s.produitId, 'nom'),
      site: await resolveField('boutiques', s.boutiqueId ?? null, 'nom'),
      type: 'boutique',
      quantite: s.quantite,
      seuil_alerte: s.seuilAlerte,
    })),
  );

  return Response.json({
    produits_actifs: produitsActifsSnap.data().count,
    stock_critique: stocksMagasinCritiques.length + stocksBoutiqueCritiques.length,
    ventes_jour: ventes.length,
    ca_jour: ventes.reduce((sum, v) => sum + v.montantTotal, 0),
    alertes: [...alertesMagasin, ...alertesBoutique],
  });
});
