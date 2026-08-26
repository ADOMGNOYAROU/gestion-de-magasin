import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth.service';

interface Transfert {
  id: string;
  produit: string | null;
  magasin: string | null;
  boutique: string | null;
  quantite: number;
  date: string;
}

interface Option {
  id: string;
  nom: string;
}

const API = `${environment.apiBaseUrl}/api/transferts`;

@Component({
  selector: 'app-transferts',
  imports: [ReactiveFormsModule],
  templateUrl: './transferts.html',
  styleUrl: './transferts.scss',
})
export class Transferts {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);

  protected readonly isAdmin = () => this.authService.user()?.role === 'admin';

  protected readonly transferts = signal<Transfert[]>([]);
  protected readonly produits = signal<Option[]>([]);
  protected readonly magasins = signal<Option[]>([]);
  protected readonly boutiques = signal<Option[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly stockDisponible = signal<number | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    produitId: ['', Validators.required],
    magasinId: ['', Validators.required],
    boutiqueId: ['', Validators.required],
    quantite: [1, [Validators.required, Validators.min(1)]],
    notes: [''],
  });

  constructor() {
    this.load();
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/produits`)
      .subscribe({ next: (res) => this.produits.set(res) });
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/boutiques`)
      .subscribe({ next: (res) => this.boutiques.set(res) });
    if (this.isAdmin()) {
      this.http
        .get<Option[]>(`${environment.apiBaseUrl}/api/magasins`)
        .subscribe({ next: (res) => this.magasins.set(res) });
    }

    this.form.valueChanges.subscribe(({ produitId, magasinId }) => {
      if (!produitId || !magasinId) {
        this.stockDisponible.set(null);
        return;
      }
      this.http
        .get<{ quantite: number }>(`${environment.apiBaseUrl}/api/stock-disponible`, {
          params: { produit_id: produitId, magasin_id: magasinId },
        })
        .subscribe({ next: (res) => this.stockDisponible.set(res.quantite) });
    });
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Transfert[]>(API).subscribe({
      next: (res) => {
        this.transferts.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les transferts.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    const magasinId = this.isAdmin() ? '' : (this.authService.user()?.magasinId ?? '');
    this.form.reset({ magasinId, quantite: 1 });
    this.showForm.set(true);
  }

  protected cancel(): void {
    this.showForm.set(false);
  }

  protected submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const v = this.form.getRawValue();
    const payload = {
      produitId: v.produitId,
      magasinId: v.magasinId,
      boutiqueId: v.boutiqueId,
      quantite: v.quantite,
      notes: v.notes || null,
    };

    this.error.set(null);
    this.http.post(API, payload).subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'enregistrement du transfert."),
    });
  }
}
