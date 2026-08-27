import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Client {
  id: string;
  nom: string;
  prenom: string | null;
  email: string | null;
  telephone: string | null;
  adresse: string | null;
}

const API = `${environment.apiBaseUrl}/api/clients`;

@Component({
  selector: 'app-clients',
  imports: [ReactiveFormsModule],
  templateUrl: './clients.html',
  styleUrl: './clients.scss',
})
export class Clients {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly clients = signal<Client[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);
  protected readonly search = signal('');

  protected readonly form = this.fb.nonNullable.group({
    nom: ['', Validators.required],
    prenom: [''],
    email: ['', Validators.email],
    telephone: [''],
    adresse: [''],
  });

  constructor() {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<Client[]>(API).subscribe({
      next: (res) => {
        this.clients.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les clients.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset();
    this.showForm.set(true);
  }

  protected openEdit(c: Client): void {
    this.editingId.set(c.id);
    this.form.setValue({
      nom: c.nom,
      prenom: c.prenom ?? '',
      email: c.email ?? '',
      telephone: c.telephone ?? '',
      adresse: c.adresse ?? '',
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
      prenom: v.prenom || null,
      email: v.email || null,
      telephone: v.telephone || null,
      adresse: v.adresse || null,
    };

    const id = this.editingId();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    this.error.set(null);
    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'enregistrement du client."),
    });
  }

  protected remove(c: Client): void {
    if (!confirm(`Supprimer "${c.nom}" ?`)) return;
    this.http.delete(`${API}/${c.id}`).subscribe({
      next: () => this.load(),
      error: (err) => this.error.set(err.error?.message ?? 'Échec de la suppression.'),
    });
  }

  protected readonly filtered = () => {
    const term = this.search().trim().toLowerCase();
    if (!term) return this.clients();
    return this.clients().filter(
      (c) => c.nom.toLowerCase().includes(term) || (c.telephone ?? '').includes(term),
    );
  };
}
