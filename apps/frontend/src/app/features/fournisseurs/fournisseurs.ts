import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Fournisseur {
  id: string;
  nom: string;
  adresse: string;
  telephone: string;
  email: string;
  contact_personne: string;
}

const API = `${environment.apiBaseUrl}/api/fournisseurs`;

@Component({
  selector: 'app-fournisseurs',
  imports: [ReactiveFormsModule],
  templateUrl: './fournisseurs.html',
  styleUrl: './fournisseurs.scss',
})
export class Fournisseurs {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly fournisseurs = signal<Fournisseur[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    adresse: ['', Validators.required],
    telephone: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    contactPersonne: ['', Validators.required],
  });

  constructor() {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Fournisseur[]>(API).subscribe({
      next: (res) => {
        this.fournisseurs.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les fournisseurs.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset();
    this.showForm.set(true);
  }

  protected openEdit(f: Fournisseur): void {
    this.editingId.set(f.id);
    this.form.setValue({
      nom: f.nom,
      adresse: f.adresse,
      telephone: f.telephone,
      email: f.email,
      contactPersonne: f.contact_personne,
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

    const id = this.editingId();
    const payload = this.form.getRawValue();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: () => this.error.set("Échec de l'enregistrement du fournisseur."),
    });
  }

  protected remove(f: Fournisseur): void {
    if (!confirm(`Supprimer "${f.nom}" ?`)) return;
    this.http.delete(`${API}/${f.id}`).subscribe({
      next: () => this.load(),
      error: () => this.error.set('Échec de la suppression.'),
    });
  }
}
