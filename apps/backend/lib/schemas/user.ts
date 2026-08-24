import { z } from 'zod';

// Reprend app/Http/Controllers/Api/UserController.php::store/update.
export const createUserSchema = z.object({
  name: z.string().min(1).max(255),
  email: z.string().email().max(255),
  password: z.string().min(6),
  role: z.enum(['admin', 'gestionnaire', 'vendeur']),
  magasinId: z.string().nullable().optional(),
  boutiqueId: z.string().nullable().optional(),
});

export const updateUserSchema = z.object({
  name: z.string().min(1).max(255),
  email: z.string().email().max(255),
  password: z.string().min(6).nullable().optional(),
  role: z.enum(['admin', 'gestionnaire', 'vendeur']),
  magasinId: z.string().nullable().optional(),
  boutiqueId: z.string().nullable().optional(),
});
