import { Component, computed, inject } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService, hasRole } from '../../core/auth.service';

interface NavItem {
  label: string;
  path: string;
  minRole: 'vendeur' | 'gestionnaire' | 'admin';
  icon: string;
}

interface NavGroup {
  label: string | null;
  items: NavItem[];
}

// Icônes en ligne (pas de dépendance à une librairie d'icônes) — style trait,
// cohérent avec le reste de l'UI. Contenu statique et fixe, jamais dérivé de
// données utilisateur : sûr à injecter via innerHTML.
const ICONS: Record<string, string> = {
  dashboard: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="10" width="4" height="10"/><rect x="10" y="5" width="4" height="15"/><rect x="17" y="13" width="4" height="7"/></svg>`,
  pos: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h2l2.6 12.6a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L21 8H6"/></svg>`,
  ventes: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l3 3v17l-3-2-3 2-3-2-3 2V2Z"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="9" y1="12" x2="15" y2="12"/></svg>`,
  produits: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="M3 8l9 5 9-5"/><line x1="12" y1="13" x2="12" y2="21"/></svg>`,
  stocks: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="M3 12l9 5 9-5"/><path d="M3 16l9 5 9-5"/></svg>`,
  entrees: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12v7a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-7"/><path d="M12 3v9"/><path d="m8 9 4 4 4-4"/><path d="M3 12h5l1 2h6l1-2h5"/></svg>`,
  transferts: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h13"/><path d="m14 3 3 4-3 4"/><path d="M20 17H7"/><path d="m10 13-3 4 3 4"/></svg>`,
  boutiques: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9 4 4h16l1 5"/><path d="M3 9a2 2 0 0 0 4 0 2 2 0 0 0 4 0 2 2 0 0 0 4 0 2 2 0 0 0 4 0"/><path d="M5 9v11h14V9"/><path d="M10 20v-6h4v6"/></svg>`,
  fournisseurs: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="7" width="13" height="10"/><path d="M14 10h4l3 3v4h-7z"/><circle cx="6" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>`,
  partenaires: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="9" r="5"/><circle cx="15" cy="15" r="5"/></svg>`,
  rapports: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l3 3v17H6Z"/><path d="M9 17v-3M12.5 17v-6M16 17v-2"/></svg>`,
  magasins: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18"/><path d="M9 21v-5h6v5"/><rect x="8" y="7" width="2" height="2"/><rect x="14" y="7" width="2" height="2"/><rect x="8" y="12" width="2" height="2"/><rect x="14" y="12" width="2" height="2"/></svg>`,
  utilisateurs: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="18" cy="9" r="2.2"/><path d="M15.5 14a5 5 0 0 1 5 5"/></svg>`,
  aide: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.3 9a2.7 2.7 0 0 1 5.2.9c0 1.6-2.2 2.1-2.2 3.6"/><path d="M12 17h.01"/></svg>`,
};

const LOGO_MARK = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9 4 4h16l1 5"/><path d="M4 9v10.5A1.5 1.5 0 0 0 5.5 21h13a1.5 1.5 0 0 0 1.5-1.5V9"/><path d="M9 21v-6h6v6"/></svg>`;

const NAV_GROUPS: NavGroup[] = [
  { label: null, items: [{ label: 'Tableau de bord', path: '/', minRole: 'vendeur', icon: ICONS['dashboard'] }] },
  {
    label: 'Vente',
    items: [
      { label: 'POS (Caisse)', path: '/pos', minRole: 'vendeur', icon: ICONS['pos'] },
      { label: 'Ventes', path: '/ventes', minRole: 'vendeur', icon: ICONS['ventes'] },
    ],
  },
  {
    label: 'Catalogue & stock',
    items: [
      { label: 'Produits', path: '/produits', minRole: 'gestionnaire', icon: ICONS['produits'] },
      { label: 'Stocks', path: '/stocks', minRole: 'gestionnaire', icon: ICONS['stocks'] },
      { label: 'Entrées de stock', path: '/entrees-stock', minRole: 'gestionnaire', icon: ICONS['entrees'] },
      { label: 'Transferts', path: '/transferts', minRole: 'gestionnaire', icon: ICONS['transferts'] },
    ],
  },
  {
    label: 'Réseau',
    items: [
      { label: 'Boutiques', path: '/boutiques', minRole: 'gestionnaire', icon: ICONS['boutiques'] },
      { label: 'Fournisseurs', path: '/fournisseurs', minRole: 'gestionnaire', icon: ICONS['fournisseurs'] },
      { label: 'Partenaires', path: '/partenaires', minRole: 'gestionnaire', icon: ICONS['partenaires'] },
    ],
  },
  {
    label: 'Pilotage',
    items: [{ label: 'Rapports', path: '/rapports', minRole: 'gestionnaire', icon: ICONS['rapports'] }],
  },
  {
    label: 'Administration',
    items: [
      { label: 'Magasins', path: '/magasins', minRole: 'admin', icon: ICONS['magasins'] },
      { label: 'Utilisateurs', path: '/utilisateurs', minRole: 'admin', icon: ICONS['utilisateurs'] },
    ],
  },
  {
    label: null,
    items: [{ label: 'Aide', path: '/aide', minRole: 'vendeur', icon: ICONS['aide'] }],
  },
];

@Component({
  selector: 'app-shell',
  imports: [RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './shell.html',
  styleUrl: './shell.scss',
})
export class Shell {
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);
  private readonly sanitizer = inject(DomSanitizer);

  protected readonly user = this.authService.user;
  protected readonly logoMark: SafeHtml = this.sanitizer.bypassSecurityTrustHtml(LOGO_MARK);

  protected readonly navGroups = computed(() => {
    const role = this.user()?.role ?? null;
    return NAV_GROUPS.map((group) => ({
      label: group.label,
      items: group.items.filter((item) => hasRole(role, item.minRole)),
    })).filter((group) => group.items.length > 0);
  });

  protected icon(svg: string): SafeHtml {
    return this.sanitizer.bypassSecurityTrustHtml(svg);
  }

  protected async logout(): Promise<void> {
    await this.authService.logout();
    await this.router.navigateByUrl('/login');
  }
}
