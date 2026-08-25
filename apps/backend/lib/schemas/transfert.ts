import { z } from 'zod';

// Reprend app/Http/Controllers/Api/TransfertController.php::store.
export const transfertSchema = z.object({
  produitId: z.string().min(1),
  magasinId: z.string().min(1),
  boutiqueId: z.string().min(1),
  quantite: z.number().int().min(1),
  notes: z.string().nullable().optional(),
});

export type TransfertInput = z.infer<typeof transfertSchema>;
