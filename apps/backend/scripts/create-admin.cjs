// Script de bootstrap : crée un compte admin (Firebase Auth + profil Firestore +
// custom claims). Nécessaire car l'app n'a pas d'auto-inscription — seul un admin
// peut créer des comptes (cf. routes/web.php: Route::resource('users')->middleware('admin')),
// donc le tout premier compte doit être créé hors API.
//
// Usage : node --env-file=.env.local scripts/create-admin.cjs <email> <password> <name>

const { cert, initializeApp } = require('firebase-admin/app');
const { getAuth } = require('firebase-admin/auth');
const { getFirestore } = require('firebase-admin/firestore');

const [, , email, password, ...nameParts] = process.argv;
const name = nameParts.join(' ');

if (!email || !password || !name) {
  console.error('Usage: node --env-file=.env.local scripts/create-admin.cjs <email> <password> "<name>"');
  process.exit(1);
}

const app = initializeApp({
  credential: cert({
    projectId: process.env.FIREBASE_PROJECT_ID,
    clientEmail: process.env.FIREBASE_CLIENT_EMAIL,
    privateKey: process.env.FIREBASE_PRIVATE_KEY.replace(/\\n/g, '\n'),
  }),
});

const auth = getAuth(app);
const db = getFirestore(app);

(async () => {
  const userRecord = await auth.createUser({ email, password, displayName: name });

  await auth.setCustomUserClaims(userRecord.uid, {
    role: 'admin',
    magasinId: null,
    boutiqueId: null,
  });

  await db.collection('users').doc(userRecord.uid).set({
    name,
    email,
    role: 'admin',
    magasinId: null,
    boutiqueId: null,
    createdAt: new Date().toISOString(),
  });

  console.log(`Compte admin créé : ${email} (uid: ${userRecord.uid})`);
  process.exit(0);
})().catch((err) => {
  console.error('Échec de la création du compte admin :', err.message);
  process.exit(1);
});
