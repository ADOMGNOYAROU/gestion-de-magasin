import { DecimalPipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { environment } from '../../../environments/environment';

interface VenteLigne {
  produit: string | null;
  quantite: number;
  prix_unitaire: number;
  remise: number;
  sous_total: number;
}

interface Vente {
  id: string;
  numero_ticket: string;
  boutique: string | null;
  montant_total: number;
  montant_recu: number;
  monnaie: number;
  mode_paiement: string | null;
  status: string;
  date_vente: string;
  lignes: VenteLigne[];
  payment_type: 'immediate' | 'credit' | 'mixed' | null;
  client: string | null;
  credit_id: string | null;
}

@Component({
  selector: 'app-ventes',
  imports: [DecimalPipe],
  templateUrl: './ventes.html',
  styleUrl: './ventes.scss',
})
export class Ventes {
  private readonly http = inject(HttpClient);

  protected readonly ventes = signal<Vente[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly expanded = signal<string | null>(null);

  constructor() {
    this.http.get<Vente[]>(`${environment.apiBaseUrl}/api/ventes`).subscribe({
      next: (res) => {
        this.ventes.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les ventes.');
        this.loading.set(false);
      },
    });
  }

  protected toggle(id: string): void {
    this.expanded.set(this.expanded() === id ? null : id);
  }
}
