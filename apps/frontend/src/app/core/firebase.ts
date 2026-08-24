import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';
import { environment } from '../../environments/environment';

// Angular ne parle jamais directement à Firestore : Firebase n'est utilisé
// côté client que pour l'authentification (obtenir un ID token, envoyé
// ensuite à l'API Next.js). Voir MIGRATION_ARCHITECTURE.md section 1 et 3.
const firebaseApp = initializeApp(environment.firebase);

export const auth = getAuth(firebaseApp);
