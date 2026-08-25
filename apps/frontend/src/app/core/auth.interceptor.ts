import { HttpInterceptorFn } from '@angular/common/http';
import { from, switchMap } from 'rxjs';
import { environment } from '../../environments/environment';
import { auth } from './firebase';

// Attache le ID token Firebase courant à chaque appel vers notre propre API
// (jamais vers un tiers) — c'est ce token que le backend vérifie via
// authenticate()/authenticateWithRole() (apps/backend/lib/auth.ts).
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  if (!req.url.startsWith(environment.apiBaseUrl)) {
    return next(req);
  }

  const currentUser = auth.currentUser;
  if (!currentUser) {
    return next(req);
  }

  return from(currentUser.getIdToken()).pipe(
    switchMap((token) => next(req.clone({ setHeaders: { Authorization: `Bearer ${token}` } }))),
  );
};
