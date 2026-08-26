import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth.service';

interface UserRow {
  id: string;
  name: string;
  email: string;
  role: 'admin' | 'gestionnaire' | 'vendeur';
  magasin_id: string | null;
  magasin_nom: string | null;
  boutique_id: string | null;
  boutique_nom: string | null;
}

interface Option {
  id: string;
  nom: string;
}

const API = `${environment.apiBaseUrl}/api/users`;

@Component({
  selector: 'app-utilisateurs',
  imports: [ReactiveFormsModule],
  templateUrl: './utilisateurs.html',
  styleUrl: './utilisateurs.scss',
})
export class Utilisateurs {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);

  protected readonly currentUid = () => this.authService.user()?.uid;

  protected readonly users = signal<UserRow[]>([]);
  protected readonly magasins = signal<Option[]>([]);
  protected readonly boutiques = signal<Option[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly showForm = signal(false);
  protected readonly editingId = signal<string | null>(null);

  protected readonly form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    password: [''],
    role: ['vendeur' as UserRow['role'], Validators.required],
    magasinId: [''],
    boutiqueId: [''],
  });

  constructor() {
    this.load();
    this.http.get<Option[]>(`${environment.apiBaseUrl}/api/magasins`).subscribe({ next: (res) => this.magasins.set(res) });
    this.http
      .get<Option[]>(`${environment.apiBaseUrl}/api/boutiques`)
      .subscribe({ next: (res) => this.boutiques.set(res) });
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<UserRow[]>(API).subscribe({
      next: (res) => {
        this.users.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les utilisateurs.');
        this.loading.set(false);
      },
    });
  }

  protected openCreate(): void {
    this.editingId.set(null);
    this.form.reset({ role: 'vendeur', magasinId: '', boutiqueId: '', password: '' });
    this.form.controls.password.setValidators([Validators.required, Validators.minLength(6)]);
    this.form.controls.password.updateValueAndValidity();
    this.showForm.set(true);
  }

  protected openEdit(u: UserRow): void {
    this.editingId.set(u.id);
    this.form.setValue({
      name: u.name,
      email: u.email,
      password: '',
      role: u.role,
      magasinId: u.magasin_id ?? '',
      boutiqueId: u.boutique_id ?? '',
    });
    this.form.controls.password.setValidators([Validators.minLength(6)]);
    this.form.controls.password.updateValueAndValidity();
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
    const payload: Record<string, unknown> = {
      name: v.name,
      email: v.email,
      role: v.role,
      magasinId: v.magasinId || null,
      boutiqueId: v.boutiqueId || null,
    };
    if (v.password) payload['password'] = v.password;

    const id = this.editingId();
    const request$ = id ? this.http.put(`${API}/${id}`, payload) : this.http.post(API, payload);

    this.error.set(null);
    request$.subscribe({
      next: () => {
        this.showForm.set(false);
        this.load();
      },
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'enregistrement de l'utilisateur."),
    });
  }

  protected remove(u: UserRow): void {
    if (!confirm(`Supprimer "${u.name}" ?`)) return;
    this.error.set(null);
    this.http.delete(`${API}/${u.id}`).subscribe({
      next: () => this.load(),
      error: (err) => this.error.set(err.error?.message ?? 'Échec de la suppression.'),
    });
  }
}
