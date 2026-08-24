import type { DecodedIdToken } from 'firebase-admin/auth';
import { getAdminAuth } from './firebase-admin';

export type Role = 'admin' | 'gestionnaire' | 'vendeur';

// Hiérarchie des rôles reprise de app/Http/Middleware/{Admin,Gestionnaire,Vendeur}Middleware.php :
// chaque rôle donne accès à son propre niveau et à tout ce qui est en dessous.
const ROLE_LEVEL: Record<Role, number> = {
  admin: 3,
  gestionnaire: 2,
  vendeur: 1,
};

export class ApiError extends Error {
  constructor(
    public status: number,
    message: string,
  ) {
    super(message);
  }
}

export interface AuthUser {
  uid: string;
  role: Role;
  magasinId: string | null;
  boutiqueId: string | null;
}

function decodedTokenToAuthUser(decoded: DecodedIdToken): AuthUser {
  const role = decoded['role'] as Role | undefined;
  if (!role || !(role in ROLE_LEVEL)) {
    throw new ApiError(403, 'Aucun rôle valide associé à ce compte');
  }

  return {
    uid: decoded.uid,
    role,
    magasinId: (decoded['magasinId'] as string | undefined) ?? null,
    boutiqueId: (decoded['boutiqueId'] as string | undefined) ?? null,
  };
}

// Vérifie le Bearer ID token Firebase envoyé par Angular et retourne l'utilisateur authentifié.
// Lève une ApiError(401) si le token est absent/invalide.
export async function authenticate(request: Request): Promise<AuthUser> {
  const header = request.headers.get('authorization');
  const token = header?.startsWith('Bearer ') ? header.slice('Bearer '.length) : null;

  if (!token) {
    throw new ApiError(401, 'Authentification requise');
  }

  try {
    const decoded = await getAdminAuth().verifyIdToken(token);
    return decodedTokenToAuthUser(decoded);
  } catch {
    throw new ApiError(401, 'Token invalide ou expiré');
  }
}

// Authentifie puis vérifie que le rôle de l'utilisateur atteint au moins `minimumRole`.
export async function authenticateWithRole(request: Request, minimumRole: Role): Promise<AuthUser> {
  const user = await authenticate(request);
  if (ROLE_LEVEL[user.role] < ROLE_LEVEL[minimumRole]) {
    throw new ApiError(403, `Accès réservé aux rôles ${minimumRole} et supérieurs`);
  }
  return user;
}
