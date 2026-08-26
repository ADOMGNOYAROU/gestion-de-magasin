import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth.service';

interface Boutique {
  id: string;
  nom: string;
  adresse: string;
  telephone: string;
  email: string | null;
  magasin_id: string;
  magasin_nom: string | null;
  vendeur_id: string | null;
  vendeur: string | null;
}

interface Option {
  id: string;
  name?: string;
  nom?: string;
  role?: string;
}

const API = `${environment.apiBaseUrl}/api/boutiques`;

@Component({
  selector: 'app-boutiques',
  imports: [ReactiveFormsModule],
  templateUrl: './boutiques.html',
  styleUrl: './boutiques.scss',
})
export class Boutiques {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);

  protected readonly isAdmin = () => this.authService.user()?.role === 'admin';

  protected readonly boutiques = signal<Boutique[]>([]);
  protected readonly magasins = signal<Option[]>([]);
  protected readonly vendeurs = signal<Option[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    adresse: ['', Validators.required],
    telephone: ['', Validators.required],
    email: [''],
    magasinId: ['', Validators.required],
    vendeurId: [''],
  });

  constructor() {
    this.load();

    this.http.get<Option[]>(`${environment.apiBaseUrl}/api/users`).subscribe({
      next: (users) => this.vendeurs.set(users.filter((u) => u.role === 'vendeur')),
    });

    if (this.isAdmin()) {
      this.http.get<Option[]>(`${environment.apiBaseUrl}/api/magasins`).subscribe({
        next: (res) => this.magasins.set(res),
      });
    }
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Boutique[]>(API).subscribe({
      next: (res) => {
        this.boutiques.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les boutiques.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    const magasinId = this.isAdmin() ? '' : (this.authService.user()?.magasinId ?? '');
    this.form.reset({ magasinId, vendeurId: '' });
    this.showForm.set(true);
  }

  protected openEdit(b: Boutique): void {
    this.editingId.set(b.id);
    this.form.setValue({
      nom: b.nom,
      adresse: b.adresse,
      telephone: b.telephone,
      email: b.email ?? '',
      magasinId: b.magasin_id,
      vendeurId: b.vendeur_id ?? '',
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
      adresse: v.adresse,
      telephone: v.telephone,
      email: v.email || null,
      magasinId: v.magasinId,
      vendeurId: v.vendeurId || null,
    };

    const id = this.editingId();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: () => this.error.set("Échec de l'enregistrement de la boutique."),
    });
  }

  protected remove(b: Boutique): void {
    if (!confirm(`Supprimer "${b.nom}" ?`)) return;
    this.http.delete(`${API}/${b.id}`).subscribe({
      next: () => this.load(),
      error: () => this.error.set('Échec de la suppression.'),
    });
  }
}
