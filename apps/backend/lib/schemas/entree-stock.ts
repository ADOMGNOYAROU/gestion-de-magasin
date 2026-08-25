import { z } from 'zod';

// Reprend app/Http/Controllers/Api/EntreeStockController.php::store.
export const entreeStockSchema = z.object({
  produitId: z.string().min(1),
  magasinId: z.string().min(1),
  fournisseurId: z.string().nullable().optional(),
  partenaireId: z.string().nullable().optional(),
  quantite: z.number().int().min(1),
  prixUnitaire: z.number().min(0),
  dateEntree: z.string().nullable().optional(),
  numeroBon: z.string().max(255).nullable().optional(),
  notes: z.string().nullable().optional(),
});

export type EntreeStockInput = z.infer<typeof entreeStockSchema>;
