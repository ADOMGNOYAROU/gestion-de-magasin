import { DecimalPipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { environment } from '../../../environments/environment';

interface AlerteStock {
  produit: string | null;
  site: string | null;
  type: 'magasin' | 'boutique';
  quantite: number;
  seuil_alerte: number;
}

interface DashboardResponse {
  produits_actifs: number;
  stock_critique: number;
  ventes_jour: number;
  ca_jour: number;
  alertes: AlerteStock[];
}

@Component({
  selector: 'app-dashboard',
  imports: [DecimalPipe],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard {
  private readonly http = inject(HttpClient);

  protected readonly data = signal<DashboardResponse | null>(null);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);

  constructor() {
    this.http.get<DashboardResponse>(`${environment.apiBaseUrl}/api/dashboard`).subscribe({
      next: (res) => {
        this.data.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger le tableau de bord.');
        this.loading.set(false);
      },
    });
  }
}
