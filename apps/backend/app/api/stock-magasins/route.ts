import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';

interface StockMagasinDoc {
  produitId: string;
  magasinId: string;
  quantite: number;
  seuilAlerte: number;
}

// Reprend StockController::magasins().
export const GET = withErrorHandling(async (request) => {
  await authenticateWithRole(request, 'gestionnaire');

  const snapshot = await getDb().collection('stocksMagasin').get();
  const stocks = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as StockMagasinDoc;
      const [produitNom, siteNom] = await Promise.all([
        resolveField('produits', data.produitId, 'nom'),
        resolveField('magasins', data.magasinId, 'nom'),
      ]);
      return {
        id: doc.id,
        produit_id: data.produitId,
        produit_nom: produitNom,
        site_id: data.magasinId,
        site_nom: siteNom,
        quantite: data.quantite,
        seuil_alerte: data.seuilAlerte,
        en_alerte: data.quantite <= data.seuilAlerte,
      };
    }),
  );

  return Response.json(stocks);
});
