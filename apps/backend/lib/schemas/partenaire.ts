import { z } from 'zod';

// Reprend app/Http/Controllers/Api/PartenaireController.php::store/update.
export const partenaireSchema = z.object({
  nom: z.string().min(1).max(255),
  adresse: z.string().min(1).max(255),
  telephone: z.string().min(1).max(255),
  email: z.string().email().max(255),
  typePartenariat: z.string().min(1).max(255),
});

export type PartenaireInput = z.infer<typeof partenaireSchema>;
