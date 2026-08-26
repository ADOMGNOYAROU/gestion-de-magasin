import { Routes } from '@angular/router';
import { authGuard, roleGuard } from './core/auth.guard';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./features/auth/login/login').then((m) => m.Login),
  },
  {
    path: '',
    loadComponent: () => import('./layout/shell/shell').then((m) => m.Shell),
    canActivate: [authGuard],
    children: [
      {
        path: '',
        loadComponent: () => import('./features/dashboard/dashboard').then((m) => m.Dashboard),
      },
      {
        path: 'produits',
        loadComponent: () => import('./features/produits/produits').then((m) => m.Produits),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'magasins',
        loadComponent: () => import('./features/magasins/magasins').then((m) => m.Magasins),
        canActivate: [roleGuard('admin')],
      },
      {
        path: 'boutiques',
        loadComponent: () => import('./features/boutiques/boutiques').then((m) => m.Boutiques),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'fournisseurs',
        loadComponent: () => import('./features/fournisseurs/fournisseurs').then((m) => m.Fournisseurs),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'partenaires',
        loadComponent: () => import('./features/partenaires/partenaires').then((m) => m.Partenaires),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'stocks',
        loadComponent: () => import('./features/stocks/stocks').then((m) => m.Stocks),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'entrees-stock',
        loadComponent: () => import('./features/entrees-stock/entrees-stock').then((m) => m.EntreesStock),
        canActivate: [roleGuard('gestionnaire')],
      },
      {
        path: 'transferts',
        loadComponent: () => import('./features/transferts/transferts').then((m) => m.Transferts),
        canActivate: [roleGuard('gestionnaire')],
      },
      // Les autres modules métier (POS, ventes, rapports, utilisateurs) viennent ensuite.
    ],
  },
  { path: '**', redirectTo: '' },
];
