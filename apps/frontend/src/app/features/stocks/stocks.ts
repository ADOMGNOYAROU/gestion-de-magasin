import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { environment } from '../../../environments/environment';

interface StockLigne {
  id: string;
  produit_id: string;
  produit_nom: string | null;
  site_id: string;
  site_nom: string | null;
  quantite: number;
  seuil_alerte: number;
  en_alerte: boolean;
}

@Component({
  selector: 'app-stocks',
  imports: [],
  templateUrl: './stocks.html',
  styleUrl: './stocks.scss',
})
export class Stocks {
  private readonly http = inject(HttpClient);

  protected readonly onglet = signal<'magasins' | 'boutiques'>('magasins');
  protected readonly stocksMagasins = signal<StockLigne[]>([]);
  protected readonly stocksBoutiques = signal<StockLigne[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);

  constructor() {
    Promise.all([
      firstValueFrom(this.http.get<StockLigne[]>(`${environment.apiBaseUrl}/api/stock-magasins`)),
      firstValueFrom(this.http.get<StockLigne[]>(`${environment.apiBaseUrl}/api/stock-boutiques`)),
    ])
      .then(([magasins, boutiques]) => {
        this.stocksMagasins.set(magasins ?? []);
        this.stocksBoutiques.set(boutiques ?? []);
        this.loading.set(false);
      })
      .catch(() => {
        this.error.set('Impossible de charger les stocks.');
        this.loading.set(false);
      });
  }

  protected readonly current = () => (this.onglet() === 'magasins' ? this.stocksMagasins() : this.stocksBoutiques());
}
