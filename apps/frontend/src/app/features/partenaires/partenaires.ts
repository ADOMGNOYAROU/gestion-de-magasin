import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Partenaire {
  id: string;
  nom: string;
  adresse: string;
  telephone: string;
  email: string;
  type_partenariat: string;
}

const API = `${environment.apiBaseUrl}/api/partenaires`;

@Component({
  selector: 'app-partenaires',
  imports: [ReactiveFormsModule],
  templateUrl: './partenaires.html',
  styleUrl: './partenaires.scss',
})
export class Partenaires {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly partenaires = signal<Partenaire[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    adresse: ['', Validators.required],
    telephone: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    typePartenariat: ['', Validators.required],
  });

  constructor() {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Partenaire[]>(API).subscribe({
      next: (res) => {
        this.partenaires.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les partenaires.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset();
    this.showForm.set(true);
  }

  protected openEdit(p: Partenaire): void {
    this.editingId.set(p.id);
    this.form.setValue({
      nom: p.nom,
      adresse: p.adresse,
      telephone: p.telephone,
      email: p.email,
      typePartenariat: p.type_partenariat,
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
      error: () => this.error.set("Échec de l'enregistrement du partenaire."),
    });
  }

  protected remove(p: Partenaire): void {
    if (!confirm(`Supprimer "${p.nom}" ?`)) return;
    this.http.delete(`${API}/${p.id}`).subscribe({
      next: () => this.load(),
      error: () => this.error.set('Échec de la suppression.'),
    });
  }
}
