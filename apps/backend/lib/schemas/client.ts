import { z } from 'zod';

// Reprend ClientController::store/update (centro).
export const clientSchema = z.object({
  nom: z.string().min(1).max(255),
  prenom: z.string().max(255).nullable().optional(),
  email: z.string().email().max(255).nullable().optional(),
  telephone: z.string().max(20).nullable().optional(),
  adresse: z.string().nullable().optional(),
});

export type ClientInput = z.infer<typeof clientSchema>;
