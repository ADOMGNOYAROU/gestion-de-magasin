import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { environment } from '../../../environments/environment';

const API = environment.apiBaseUrl;

function today(): string {
  return new Date().toISOString().slice(0, 10);
}

function firstDayOfMonth(): string {
  const d = new Date();
  return new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0, 10);
}

function triggerDownload(blob: Blob, filename: string): void {
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

@Component({
  selector: 'app-rapports',
  imports: [FormsModule],
  templateUrl: './rapports.html',
  styleUrl: './rapports.scss',
})
export class Rapports {
  private readonly http = inject(HttpClient);

  protected readonly dateDebut = signal(firstDayOfMonth());
  protected readonly dateFin = signal(today());
  protected readonly loadingKey = signal<string | null>(null);
  protected readonly error = signal<string | null>(null);

  protected telechargerStock(): void {
    this.download('stock', `${API}/api/rapports/stock`, 'rapport_stock.pdf');
  }

  protected telechargerPartenaires(): void {
    this.download('partenaires', `${API}/api/rapports/partenaires`, 'rapport_partenaires.pdf');
  }

  protected telechargerVentes(format: 'pdf' | 'excel'): void {
    const ext = format === 'excel' ? 'xlsx' : 'pdf';
    const url = `${API}/api/rapports/ventes?date_debut=${this.dateDebut()}&date_fin=${this.dateFin()}&format=${format}`;
    this.download(`ventes-${format}`, url, `rapport_ventes_${this.dateDebut()}_au_${this.dateFin()}.${ext}`);
  }

  private download(key: string, url: string, filename: string): void {
    this.loadingKey.set(key);
    this.error.set(null);
    this.http.get(url, { responseType: 'blob' }).subscribe({
      next: (blob) => {
        triggerDownload(blob, filename);
        this.loadingKey.set(null);
      },
      error: () => {
        this.error.set('Échec de la génération du rapport.');
        this.loadingKey.set(null);
      },
    });
  }
}
