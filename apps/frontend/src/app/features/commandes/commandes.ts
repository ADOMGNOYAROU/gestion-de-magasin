import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth.service';

interface Order {
  id: string;
  numero_commande: string;
  fournisseur: string | null;
  date_commande: string;
  date_livraison_prevue: string | null;
  status: 'en_cours' | 'livree' | 'annulee';
  montant_total: number;
}

interface Option {
  id: string;
  nom: string;
}

interface Ligne {
  produitId: string;
  produitNom: string;
  quantite: number;
  prixUnitaire: number;
}

const API = `${environment.apiBaseUrl}/api/orders`;

@Component({
  selector: 'app-commandes',
  imports: [ReactiveFormsModule],
  templateUrl: './commandes.html',
  styleUrl: './commandes.scss',
})
export class Commandes {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);

  protected readonly isAdmin = () => this.authService.user()?.role === 'admin';

  protected readonly orders = signal<Order[]>([]);
  protected readonly fournisseurs = signal<Option[]>([]);
  protected readonly magasins = signal<Option[]>([]);
  protected readonly produits = signal<Option[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly lignes = signal<Ligne[]>([]);

  protected readonly form = this.fb.nonNullable.group({
    fournisseurId: ['', Validators.required],
    magasinId: ['', Validators.required],
    dateLivraisonPrevue: [''],
    notes: [''],
  });

  protected readonly ligneForm = this.fb.nonNullable.group({
    produitId: [''],
    quantite: [1, Validators.min(1)],
    prixUnitaire: [0, Validators.min(0)],
  });

  constructor() {
    this.load();
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/fournisseurs`)
      .subscribe({ next: (res) => this.fournisseurs.set(res) });
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/produits`)
      .subscribe({ next: (res) => this.produits.set(res) });
    if (this.isAdmin()) {
      this.http
        .get<Option[]>(`${environment.apiBaseUrl}/api/magasins`)
        .subscribe({ next: (res) => this.magasins.set(res) });
    }
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Order[]>(API).subscribe({
      next: (res) => {
        this.orders.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les commandes.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    const magasinId = this.isAdmin() ? '' : (this.authService.user()?.magasinId ?? '');
    this.form.reset({ magasinId });
    this.lignes.set([]);
    this.showForm.set(true);
  }

  protected cancelForm(): void {
    this.showForm.set(false);
  }

  protected ajouterLigne(): void {
    const v = this.ligneForm.getRawValue();
    if (!v.produitId || v.quantite < 1) return;
    const produit = this.produits().find((p) => p.id === v.produitId);
    if (!produit) return;
    this.lignes.set([
      ...this.lignes(),
      { produitId: v.produitId, produitNom: produit.nom, quantite: v.quantite, prixUnitaire: v.prixUnitaire },
    ]);
    this.ligneForm.reset({ produitId: '', quantite: 1, prixUnitaire: 0 });
  }

  protected retirerLigne(index: number): void {
    this.lignes.set(this.lignes().filter((_, i) => i !== index));
  }

  protected readonly totalLignes = () => this.lignes().reduce((sum, l) => sum + l.quantite * l.prixUnitaire, 0);

  protected submit(): void {
    if (this.form.invalid || this.lignes().length === 0) {
      this.form.markAllAsTouched();
      if (this.lignes().length === 0) this.error.set('Ajoutez au moins une ligne de produit.');
      return;
    }

    const v = this.form.getRawValue();
    const payload = {
      fournisseurId: v.fournisseurId,
      magasinId: v.magasinId,
      dateLivraisonPrevue: v.dateLivraisonPrevue || null,
      notes: v.notes || null,
      lignes: this.lignes().map((l) => ({ produitId: l.produitId, quantite: l.quantite, prixUnitaire: l.prixUnitaire })),
    };

    this.error.set(null);
    this.http.post(API, payload).subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'enregistrement de la commande."),
    });
  }

  protected livrer(order: Order): void {
    if (!confirm(`Marquer la commande ${order.numero_commande} comme livrée ? Le stock du magasin sera mis à jour.`)) return;
    this.http.post(`${API}/${order.id}/livrer`, {}).subscribe({
      next: () => this.load(),
      error: (err) => this.error.set(err.error?.message ?? 'Échec de la livraison.'),
    });
  }

  protected annuler(order: Order): void {
    if (!confirm(`Annuler la commande ${order.numero_commande} ?`)) return;
    this.http.delete(`${API}/${order.id}`).subscribe({
      next: () => this.load(),
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'annulation."),
    });
  }
}
