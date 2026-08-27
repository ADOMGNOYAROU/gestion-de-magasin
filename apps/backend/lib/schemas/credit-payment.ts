import { z } from 'zod';

// Reprend CreditController::storePayment (centro).
export const creditPaymentSchema = z.object({
  amount: z.number().positive(),
  paymentDate: z.string().min(1),
  notes: z.string().max(500).nullable().optional(),
});

export type CreditPaymentInput = z.infer<typeof creditPaymentSchema>;
