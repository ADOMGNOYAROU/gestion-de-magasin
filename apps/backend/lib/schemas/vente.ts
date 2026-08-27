import { z } from 'zod';

// Reprend VenteController::store() (API) + le paiement à crédit/mixte de
// centro (VenteController web + CreditController). client requis dès que le
// paiement n'est pas intégralement immédiat.
const ligneSchema = z.object({
  produitId: z.string().min(1),
  quantite: z.number().int().min(1),
  remise: z.number().min(0).nullable().optional(),
});

export const venteSchema = z
  .object({
    boutiqueId: z.string().nullable().optional(),
    paymentMethodId: z.string().min(1),
    paymentType: z.enum(['immediate', 'credit', 'mixed']).default('immediate'),
    clientId: z.string().nullable().optional(),
    montantRecu: z.number().min(0),
    lignes: z.array(ligneSchema).min(1),
  })
  .refine((v) => v.paymentType === 'immediate' || !!v.clientId, {
    message: 'Un client est requis pour une vente à crédit ou en paiement mixte.',
    path: ['clientId'],
  });

export type VenteInput = z.infer<typeof venteSchema>;
