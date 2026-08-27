import { DecimalPipe } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { environment } from '../../../environments/environment';

interface Session {
  id: string;
  boutique_id: string;
  montant_initial: number;
  montant_theorique: number;
  status: string;
  date_ouverture: string;
}

interface Produit {
  id: string;
  nom: string;
  categorie: string;
  prix_vente: number;
  statut: string;
}

interface PaymentMethod {
  id: string;
  name: string;
  code: string;
}

interface Client {
  id: string;
  nom: string;
  prenom: string | null;
}

interface CartLine {
  produit: Produit;
  quantite: number;
  remise: number;
}

interface VenteResult {
  id: string;
  numero_ticket: string;
  montant_total: number;
  monnaie: number;
}

const API = environment.apiBaseUrl;

@Component({
  selector: 'app-pos',
  imports: [FormsModule, DecimalPipe],
  templateUrl: './pos.html',
  styleUrl: './pos.scss',
})
export class Pos {
  private readonly http = inject(HttpClient);

  protected readonly session = signal<Session | null>(null);
  protected readonly loadingSession = signal(true);
  protected readonly montantInitial = signal(0);

  protected readonly produits = signal<Produit[]>([]);
  protected readonly paymentMethods = signal<PaymentMethod[]>([]);
  protected readonly clients = signal<Client[]>([]);
  protected readonly search = signal('');
  protected readonly cart = signal<CartLine[]>([]);
  protected readonly paymentMethodId = signal('');
  protected readonly montantRecu = signal(0);
  protected readonly paymentType = signal<'immediate' | 'credit' | 'mixed'>('immediate');
  protected readonly clientId = signal('');

  protected readonly error = signal<string | null>(null);
  protected readonly dernierTicket = signal<VenteResult | null>(null);
  protected readonly submitting = signal(false);

  protected readonly montantFinalFermeture = signal(0);

  protected readonly total = computed(() =>
    this.cart().reduce((sum, l) => sum + (l.produit.prix_vente - l.remise) * l.quantite, 0),
  );

  protected readonly filteredProduits = computed(() => {
    const term = this.search().trim().toLowerCase();
    const actifs = this.produits().filter((p) => p.statut === 'actif');
    if (!term) return actifs;
    return actifs.filter((p) => p.nom.toLowerCase().includes(term));
  });

  constructor() {
    this.refreshSession();
    this.http.get<Produit[]>(`${API}/api/produits`).subscribe({ next: (res) => this.produits.set(res) });
    this.http
      .get<PaymentMethod[]>(`${API}/api/payment-methods`)
      .subscribe({ next: (res) => this.paymentMethods.set(res) });
    this.http.get<Client[]>(`${API}/api/clients`).subscribe({ next: (res) => this.clients.set(res) });
  }

  private refreshSession(): void {
    this.loadingSession.set(true);
    this.http.get<Session | null>(`${API}/api/cash-register-session/current`).subscribe({
      next: (res) => {
        this.session.set(res);
        this.loadingSession.set(false);
      },
      error: () => this.loadingSession.set(false),
    });
  }

  protected ouvrirSession(): void {
    this.error.set(null);
    this.http
      .post<Session>(`${API}/api/cash-register-session/open`, { montantInitial: this.montantInitial() })
      .subscribe({
        next: (res) => this.session.set(res),
        error: (err) => this.error.set(err.error?.message ?? "Échec de l'ouverture de la session."),
      });
  }

  protected fermerSession(): void {
    const s = this.session();
    if (!s) return;
    this.error.set(null);
    this.http
      .post(`${API}/api/cash-register-session/${s.id}/close`, { montantFinal: this.montantFinalFermeture() })
      .subscribe({
        next: () => {
          this.session.set(null);
          this.cart.set([]);
        },
        error: (err) => this.error.set(err.error?.message ?? 'Échec de la fermeture de la session.'),
      });
  }

  protected ajouterAuPanier(p: Produit): void {
    const lignes = this.cart();
    const existing = lignes.find((l) => l.produit.id === p.id);
    if (existing) {
      this.cart.set(lignes.map((l) => (l.produit.id === p.id ? { ...l, quantite: l.quantite + 1 } : l)));
    } else {
      this.cart.set([...lignes, { produit: p, quantite: 1, remise: 0 }]);
    }
  }

  protected changerQuantite(produitId: string, quantite: number): void {
    if (quantite < 1) return;
    this.cart.set(this.cart().map((l) => (l.produit.id === produitId ? { ...l, quantite } : l)));
  }

  protected changerRemise(produitId: string, remise: number): void {
    this.cart.set(this.cart().map((l) => (l.produit.id === produitId ? { ...l, remise: remise || 0 } : l)));
  }

  protected retirerDuPanier(produitId: string): void {
    this.cart.set(this.cart().filter((l) => l.produit.id !== produitId));
  }

  protected encaisser(): void {
    if (this.cart().length === 0 || !this.paymentMethodId()) return;
    if (this.paymentType() !== 'immediate' && !this.clientId()) {
      this.error.set('Un client est requis pour une vente à crédit ou mixte.');
      return;
    }

    const payload = {
      paymentMethodId: this.paymentMethodId(),
      montantRecu: this.paymentType() === 'credit' ? 0 : this.montantRecu(),
      paymentType: this.paymentType(),
      clientId: this.clientId() || null,
      lignes: this.cart().map((l) => ({ produitId: l.produit.id, quantite: l.quantite, remise: l.remise })),
    };

    this.submitting.set(true);
    this.error.set(null);
    this.http.post<VenteResult>(`${API}/api/ventes`, payload).subscribe({
      next: (res) => {
        this.dernierTicket.set(res);
        this.cart.set([]);
        this.montantRecu.set(0);
        this.paymentType.set('immediate');
        this.clientId.set('');
        this.submitting.set(false);
        this.refreshSession();
      },
      error: (err) => {
        this.error.set(err.error?.message ?? "Échec de l'encaissement.");
        this.submitting.set(false);
      },
    });
  }
}
