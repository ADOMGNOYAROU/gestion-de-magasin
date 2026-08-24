import { z } from 'zod';

// Reprend app/Http/Controllers/Api/FournisseurController.php::store/update.
export const fournisseurSchema = z.object({
  nom: z.string().min(1).max(255),
  adresse: z.string().min(1).max(255),
  telephone: z.string().min(1).max(255),
  email: z.string().email().max(255),
  contactPersonne: z.string().min(1).max(255),
});

export type FournisseurInput = z.infer<typeof fournisseurSchema>;
