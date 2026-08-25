import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService, Role, hasRole } from './auth.service';

export const authGuard: CanActivateFn = async () => {
  const auth = inject(AuthService);
  const router = inject(Router);

  await auth.ready;
  if (auth.user()) return true;
  return router.parseUrl('/login');
};

// Factory de guard : roleGuard('gestionnaire') exige gestionnaire ou admin,
// même hiérarchie que hasRole()/le backend.
export function roleGuard(minimum: Role): CanActivateFn {
  return async () => {
    const auth = inject(AuthService);
    const router = inject(Router);

    await auth.ready;
    const user = auth.user();
    if (!user) return router.parseUrl('/login');
    if (hasRole(user.role, minimum)) return true;
    return router.parseUrl('/');
  };
}
