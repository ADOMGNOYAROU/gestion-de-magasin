import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface CreditRow {
  id: string;
  client: string | null;
  boutique: string | null;
  numero_ticket: string | null;
  total_amount: number;
  remaining_balance: number;
  status: 'active' | 'paid';
  created_at: string;
}

interface CreditPayment {
  id: string;
  amount: number;
  payment_date: string;
  notes: string | null;
}

interface CreditDetail extends CreditRow {
  paiements: CreditPayment[];
}

const API = `${environment.apiBaseUrl}/api/credits`;

@Component({
  selector: 'app-credits',
  imports: [ReactiveFormsModule],
  templateUrl: './credits.html',
  styleUrl: './credits.scss',
})
export class Credits {
  private readonly http = inject(HttpClient);
  private readonly fb = inject(FormBuilder);

  protected readonly credits = signal<CreditRow[]>([]);
  protected readonly loading = signal(true);
  protected readonly error = signal<string | null>(null);
  protected readonly expandedId = signal<string | null>(null);
  protected readonly detail = signal<CreditDetail | null>(null);
  protected readonly detailLoading = signal(false);

  protected readonly paymentForm = this.fb.nonNullable.group({
    amount: [0, [Validators.required, Validators.min(0.01)]],
    paymentDate: [new Date().toISOString().slice(0, 10), Validators.required],
    notes: [''],
  });

  constructor() {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.http.get<CreditRow[]>(API).subscribe({
      next: (res) => {
        this.credits.set(res);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger les crédits.');
        this.loading.set(false);
      },
    });
  }

  protected toggle(credit: CreditRow): void {
    if (this.expandedId() === credit.id) {
      this.expandedId.set(null);
      this.detail.set(null);
      return;
    }
    this.expandedId.set(credit.id);
    this.detail.set(null);
    this.detailLoading.set(true);
    this.paymentForm.reset({ amount: 0, paymentDate: new Date().toISOString().slice(0, 10), notes: '' });
    this.http.get<CreditDetail>(`${API}/${credit.id}`).subscribe({
      next: (res) => {
        this.detail.set(res);
        this.detailLoading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger le détail du crédit.');
        this.detailLoading.set(false);
      },
    });
  }

  protected ajouterPaiement(credit: CreditRow): void {
    if (this.paymentForm.invalid) {
      this.paymentForm.markAllAsTouched();
      return;
    }
    const v = this.paymentForm.getRawValue();
    this.error.set(null);
    this.http.post(`${API}/${credit.id}/paiements`, { amount: v.amount, paymentDate: v.paymentDate, notes: v.notes || null }).subscribe({
      next: () => {
        this.load();
        this.paymentForm.reset({ amount: 0, paymentDate: new Date().toISOString().slice(0, 10), notes: '' });
        this.http.get<CreditDetail>(`${API}/${credit.id}`).subscribe({ next: (res) => this.detail.set(res) });
      },
      error: (err) => this.error.set(err.error?.message ?? "Échec de l'enregistrement du paiement."),
    });
  }
}
