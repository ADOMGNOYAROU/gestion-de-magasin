import { z } from 'zod';

// Reprend CashRegisterSessionController::open/close.
export const openSessionSchema = z.object({
  boutiqueId: z.string().nullable().optional(),
  montantInitial: z.number().min(0),
});

export const closeSessionSchema = z.object({
  montantFinal: z.number().min(0),
  notes: z.string().nullable().optional(),
});
