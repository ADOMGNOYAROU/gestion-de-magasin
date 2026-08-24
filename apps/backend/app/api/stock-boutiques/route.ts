import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';

interface StockBoutiqueDoc {
  produitId: string;
  boutiqueId: string;
  quantite: number;
  seuilAlerte: number;
}

// Reprend StockController::boutiques(). Accessible dès le rôle vendeur : le POS
// a besoin de consulter le stock de sa propre boutique.
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'vendeur');

  const snapshot = await getDb().collection('stocksBoutique').get();
  const stocks = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as StockBoutiqueDoc;
      const [produitNom, siteNom] = await Promise.all([
        resolveField('produits', data.produitId, 'nom'),
        resolveField('boutiques', data.boutiqueId, 'nom'),
      ]);
      return {
        id: doc.id,
        produit_id: data.produitId,
        produit_nom: produitNom,
        site_id: data.boutiqueId,
        site_nom: siteNom,
        quantite: data.quantite,
        seuil_alerte: data.seuilAlerte,
        en_alerte: data.quantite <= data.seuilAlerte,
      };
    }),
  );

  return Response.json(stocks);
});
