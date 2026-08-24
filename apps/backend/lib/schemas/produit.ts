import { z } from 'zod';

// Reprend exactement les règles de validation de
// app/Http/Controllers/Api/ProduitController.php::validated().
export const produitSchema = z.object({
  nom: z.string().min(1).max(255),
  categorie: z.string().min(1).max(255),
  description: z.string().min(1),
  reference: z.string().max(255).nullable().optional(),
  prixAchat: z.number().min(0),
  prixVente: z.number().min(0),
  statut: z.enum(['actif', 'inactif']).nullable().optional(),
});

export type ProduitInput = z.infer<typeof produitSchema>;
