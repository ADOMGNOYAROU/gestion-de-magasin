import { z } from 'zod';

// Reprend VenteController::store().
const ligneSchema = z.object({
  produitId: z.string().min(1),
  quantite: z.number().int().min(1),
  remise: z.number().min(0).nullable().optional(),
});

export const venteSchema = z.object({
  boutiqueId: z.string().nullable().optional(),
  paymentMethodId: z.string().min(1),
  montantRecu: z.number().min(0),
  lignes: z.array(ligneSchema).min(1),
});

export type VenteInput = z.infer<typeof venteSchema>;
