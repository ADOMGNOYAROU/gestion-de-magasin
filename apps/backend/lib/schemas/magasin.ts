import { z } from 'zod';

// Reprend les règles de app/Http/Controllers/Api/MagasinController.php::store/update.
export const magasinSchema = z.object({
  nom: z.string().min(1).max(255),
  localisation: z.string().min(1).max(255),
  responsableId: z.string().nullable().optional(),
});

export type MagasinInput = z.infer<typeof magasinSchema>;
