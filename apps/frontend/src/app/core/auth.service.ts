import { Injectable, signal } from '@angular/core';
import { User, onAuthStateChanged, signInWithEmailAndPassword, signOut } from 'firebase/auth';
import { auth } from './firebase';

export type Role = 'admin' | 'gestionnaire' | 'vendeur';

export interface AppUser {
  uid: string;
  email: string | null;
  role: Role | null;
  magasinId: string | null;
  boutiqueId: string | null;
}

const ROLE_LEVEL: Record<Role, number> = { admin: 3, gestionnaire: 2, vendeur: 1 };

// Reprend la hiérarchie de rôles du backend (apps/backend/lib/auth.ts), qui
// reprend elle-même app/Http/Middleware/{Admin,Gestionnaire,Vendeur}Middleware.php.
export function hasRole(userRole: Role | null, minimum: Role): boolean {
  if (!userRole) return false;
  return ROLE_LEVEL[userRole] >= ROLE_LEVEL[minimum];
}

function toAppUser(firebaseUser: User, claims: Record<string, unknown>): AppUser {
  return {
    uid: firebaseUser.uid,
    email: firebaseUser.email,
    role: (claims['role'] as Role) ?? null,
    magasinId: (claims['magasinId'] as string) ?? null,
    boutiqueId: (claims['boutiqueId'] as string) ?? null,
  };
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  readonly user = signal<AppUser | null>(null);

  // Les guards attendent cette promesse avant de décider : onAuthStateChanged
  // est asynchrone au démarrage (lecture du token persisté), on ne peut pas
  // se fier à `user()` avant sa première résolution.
  readonly ready: Promise<void>;

  constructor() {
    let resolveReady!: () => void;
    this.ready = new Promise((resolve) => {
      resolveReady = resolve;
    });

    onAuthStateChanged(auth, async (firebaseUser) => {
      if (firebaseUser) {
        const tokenResult = await firebaseUser.getIdTokenResult();
        this.user.set(toAppUser(firebaseUser, tokenResult.claims));
      } else {
        this.user.set(null);
      }
      resolveReady();
    });
  }

  login(email: string, password: string) {
    return signInWithEmailAndPassword(auth, email, password);
  }

  logout() {
    return signOut(auth);
  }
}
