import { DecimalPipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Produit {
  id: string;
  nom: string;
  categorie: string;
  description: string;
  reference: string | null;
  prix_achat: number;
  prix_vente: number;
  statut: 'actif' | 'inactif';
  deleted_at: string | null;
}

const API = `${environment.apiBaseUrl}/api/produits`;

@Component({
  selector: 'app-produits',
  imports: [ReactiveFormsModule, DecimalPipe],
  templateUrl: './produits.html',
  styleUrl: './produits.scss',
})
export class Produits {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly produits = signal<Produit[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);
  protected readonly search = signal('');

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    categorie: ['', Validators.required],
    description: ['', Validators.required],
    reference: [''],
    prixAchat: [0, [Validators.required, Validators.min(0)]],
    prixVente: [0, [Validators.required, Validators.min(0)]],
    statut: ['actif' as 'actif' | 'inactif'],
  });

  constructor() {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Produit[]>(API).subscribe({
      next: (res) => {
        this.produits.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les produits.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ statut: 'actif', prixAchat: 0, prixVente: 0 });
    this.showForm.set(true);
  }

  protected openEdit(p: Produit): void {
    this.editingId.set(p.id);
    this.form.setValue({
      nom: p.nom,
      categorie: p.categorie,
      description: p.description,
      reference: p.reference ?? '',
      prixAchat: p.prix_achat,
      prixVente: p.prix_vente,
      statut: p.statut,
    });
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
      nom: v.nom,
      categorie: v.categorie,
      description: v.description,
      reference: v.reference || null,
      prixAchat: v.prixAchat,
      prixVente: v.prixVente,
      statut: v.statut,
    };

    const id = this.editingId();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: () => this.error.set("Échec de l'enregistrement du produit."),
    });
  }

  protected remove(p: Produit): void {
    if (!confirm(`Supprimer "${p.nom}" ?`)) return;
    this.http.delete(`${API}/${p.id}`).subscribe({
      next: () => this.load(),
      error: () => this.error.set('Échec de la suppression.'),
    });
  }

  protected readonly filtered = () => {
    const term = this.search().trim().toLowerCase();
    if (!term) return this.produits();
    return this.produits().filter(
      (p) => p.nom.toLowerCase().includes(term) || p.categorie.toLowerCase().includes(term),
    );
  };
}
