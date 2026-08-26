import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Magasin {
  id: string;
  nom: string;
  localisation: string;
  responsable_id: string | null;
  responsable: string | null;
}

interface UserOption {
  id: string;
  name: string;
  role: string;
}

const API = `${environment.apiBaseUrl}/api/magasins`;

@Component({
  selector: 'app-magasins',
  imports: [ReactiveFormsModule],
  templateUrl: './magasins.html',
  styleUrl: './magasins.scss',
})
export class Magasins {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly magasins = signal<Magasin[]>([]);
  protected readonly gestionnaires = signal<UserOption[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    localisation: ['', Validators.required],
    responsableId: [''],
  });

  constructor() {
    this.load();
    this.http.get<UserOption[]>(`${environment.apiBaseUrl}/api/users`).subscribe({
      next: (users) => this.gestionnaires.set(users.filter((u) => u.role === 'gestionnaire')),
    });
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Magasin[]>(API).subscribe({
      next: (res) => {
        this.magasins.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les magasins.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ responsableId: '' });
    this.showForm.set(true);
  }

  protected openEdit(m: Magasin): void {
    this.editingId.set(m.id);
    this.form.setValue({ nom: m.nom, localisation: m.localisation, responsableId: m.responsable_id ?? '' });
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
    const payload = { nom: v.nom, localisation: v.localisation, responsableId: v.responsableId || null };

    const id = this.editingId();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: () => this.error.set("Échec de l'enregistrement du magasin."),
    });
  }

  protected remove(m: Magasin): void {
    if (!confirm(`Supprimer "${m.nom}" ?`)) return;
    this.http.delete(`${API}/${m.id}`).subscribe({
      next: () => this.load(),
      error: () => this.error.set('Échec de la suppression.'),
    });
  }
}
