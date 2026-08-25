import { z } from 'zod';

// Reprend les règles de app/Http/Controllers/Api/BoutiqueController.php::store/update.
export const boutiqueSchema = z.object({
  nom: z.string().min(1).max(255),
  adresse: z.string().min(1).max(255),
  telephone: z.string().min(1).max(255),
  email: z.string().email().max(255).nullable().optional(),
  magasinId: z.string().min(1),
  vendeurId: z.string().nullable().optional(),
});

export type BoutiqueInput = z.infer<typeof boutiqueSchema>;
