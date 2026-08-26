import { DecimalPipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth.service';

interface EntreeStock {
  id: string;
  produit: string | null;
  magasin: string | null;
  fournisseur: string | null;
  partenaire: string | null;
  quantite: number;
  prix_unitaire: number;
  montant_total: number;
  date_entree: string;
  numero_bon: string | null;
}

interface Option {
  id: string;
  nom: string;
}

const API = `${environment.apiBaseUrl}/api/entrees-stock`;

@Component({
  selector: 'app-entrees-stock',
  imports: [ReactiveFormsModule, DecimalPipe],
  templateUrl: './entrees-stock.html',
  styleUrl: './entrees-stock.scss',
})
export class EntreesStock {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);

  protected readonly isAdmin = () => this.authService.user()?.role === 'admin';

  protected readonly entrees = signal<EntreeStock[]>([]);
  protected readonly produits = signal<Option[]>([]);
  protected readonly magasins = signal<Option[]>([]);
  protected readonly fournisseurs = signal<Option[]>([]);
  protected readonly partenaires = signal<Option[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);

  protected readonly form = this.fb.nonNullable.group({
    produitId: ['', Validators.required],
    magasinId: ['', Validators.required],
    fournisseurId: [''],
    partenaireId: [''],
    quantite: [1, [Validators.required, Validators.min(1)]],
    prixUnitaire: [0, [Validators.required, Validators.min(0)]],
    numeroBon: [''],
    notes: [''],
  });

  constructor() {
    this.load();
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/produits`)
      .subscribe({ next: (res) => this.produits.set(res) });
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/fournisseurs`)
      .subscribe({ next: (res) => this.fournisseurs.set(res) });
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/partenaires`)
      .subscribe({ next: (res) => this.partenaires.set(res) });
    if (this.isAdmin()) {
      this.http
        .get<Option[]>(`${environment.apiBaseUrl}/api/magasins`)
        .subscribe({ next: (res) => this.magasins.set(res) });
    }
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<EntreeStock[]>(API).subscribe({
      next: (res) => {
        this.entrees.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les entrées de stock.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    const magasinId = this.isAdmin() ? '' : (this.authService.user()?.magasinId ?? '');
    this.form.reset({ magasinId, quantite: 1, prixUnitaire: 0 });
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
      fournisseurId: v.fournisseurId || null,
      partenaireId: v.partenaireId || null,
      quantite: v.quantite,
      prixUnitaire: v.prixUnitaire,
      numeroBon: v.numeroBon || null,
      notes: v.notes || null,
    };

    this.http.post(API, payload).subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: () => this.error.set("Échec de l'enregistrement de l'entrée de stock."),
    });
  }
}
