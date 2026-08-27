import { z } from 'zod';

// Reprend OrderController::store/update (centro). Ajoute magasinId : le modèle
// Order d'origine n'en a pas et OrderController::livrer() incrémente le premier
// StockMagasin trouvé pour le produit, sans filtrer par magasin — ambigu dès
// qu'il y a plus d'un magasin. On corrige en rendant le magasin explicite.
const ligneSchema = z.object({
  produitId: z.string().min(1),
  quantite: z.number().int().min(1),
  prixUnitaire: z.number().min(0),
});

export const orderSchema = z.object({
  fournisseurId: z.string().min(1),
  magasinId: z.string().min(1),
  dateLivraisonPrevue: z.string().nullable().optional(),
  notes: z.string().max(1000).nullable().optional(),
  lignes: z.array(ligneSchema).min(1),
});

export type OrderInput = z.infer<typeof orderSchema>;
