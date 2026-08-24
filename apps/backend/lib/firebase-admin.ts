import { cert, getApps, initializeApp, type App } from 'firebase-admin/app';
import { getAuth, type Auth } from 'firebase-admin/auth';
import { getFirestore, type Firestore } from 'firebase-admin/firestore';

// Initialisation paresseuse : Next.js importe ce module au moment de collecter les
// routes pendant `next build`, avant que les variables d'environnement Firebase ne
// soient forcément disponibles. Initialiser au premier appel réel évite de casser le build.
let app: App | undefined;

function getFirebaseApp(): App {
  if (app) return app;

  const existing = getApps();
  if (existing.length) {
    app = existing[0];
    return app;
  }

  app = initializeApp({
    credential: cert({
      projectId: process.env.FIREBASE_PROJECT_ID,
      clientEmail: process.env.FIREBASE_CLIENT_EMAIL,
      privateKey: process.env.FIREBASE_PRIVATE_KEY?.replace(/\\n/g, '\n'),
    }),
  });
  return app;
}

let dbInstance: Firestore | undefined;
let authInstance: Auth | undefined;

export function getDb(): Firestore {
  return (dbInstance ??= getFirestore(getFirebaseApp()));
}

export function getAdminAuth(): Auth {
  return (authInstance ??= getAuth(getFirebaseApp()));
}
