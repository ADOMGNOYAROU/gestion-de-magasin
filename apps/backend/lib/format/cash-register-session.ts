export interface CashRegisterSessionDoc {
  vendeurId: string;
  boutiqueId: string;
  montantInitial: number;
  montantFinal: number | null;
  montantTheorique: number;
  ecart: number | null;
  status: 'ouverte' | 'fermee';
  dateOuverture: string;
  dateFermeture: string | null;
  notes: string | null;
}

export function formatCashRegisterSession(id: string, data: CashRegisterSessionDoc) {
  return {
    id,
    boutique_id: data.boutiqueId,
    montant_initial: data.montantInitial,
    montant_final: data.montantFinal,
    montant_theorique: data.montantTheorique,
    ecart: data.ecart,
    status: data.status,
    date_ouverture: data.dateOuverture,
    date_fermeture: data.dateFermeture,
  };
}
