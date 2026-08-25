import { Component, computed, inject } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService, hasRole } from '../../core/auth.service';

interface NavItem {
  label: string;
  path: string;
  minRole: 'vendeur' | 'gestionnaire' | 'admin';
}

const NAV_ITEMS: NavItem[] = [
  { label: 'Tableau de bord', path: '/', minRole: 'vendeur' },
  { label: 'POS (Caisse)', path: '/pos', minRole: 'vendeur' },
  { label: 'Ventes', path: '/ventes', minRole: 'vendeur' },
  { label: 'Produits', path: '/produits', minRole: 'gestionnaire' },
  { label: 'Stocks', path: '/stocks', minRole: 'gestionnaire' },
  { label: 'Entrées de stock', path: '/entrees-stock', minRole: 'gestionnaire' },
  { label: 'Transferts', path: '/transferts', minRole: 'gestionnaire' },
  { label: 'Boutiques', path: '/boutiques', minRole: 'gestionnaire' },
  { label: 'Fournisseurs', path: '/fournisseurs', minRole: 'gestionnaire' },
  { label: 'Partenaires', path: '/partenaires', minRole: 'gestionnaire' },
  { label: 'Rapports', path: '/rapports', minRole: 'gestionnaire' },
  { label: 'Magasins', path: '/magasins', minRole: 'admin' },
  { label: 'Utilisateurs', path: '/utilisateurs', minRole: 'admin' },
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

  protected readonly user = this.authService.user;

  protected readonly navItems = computed(() => {
    const role = this.user()?.role ?? null;
    return NAV_ITEMS.filter((item) => hasRole(role, item.minRole));
  });

  protected async logout(): Promise<void> {
    await this.authService.logout();
    await this.router.navigateByUrl('/login');
  }
}
