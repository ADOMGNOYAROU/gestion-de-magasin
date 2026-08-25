// Reprend database/seeders/PaymentMethodSeeder.php. Firestore n'ayant pas de
// migrations/seeders comme Laravel, ces données de référence doivent être
// créées manuellement une fois par projet Firebase.
//
// Usage : node --env-file=.env.local scripts/seed-payment-methods.cjs

const { cert, initializeApp } = require('firebase-admin/app');
const { getFirestore } = require('firebase-admin/firestore');

const app = initializeApp({
  credential: cert({
    projectId: process.env.FIREBASE_PROJECT_ID,
    clientEmail: process.env.FIREBASE_CLIENT_EMAIL,
    privateKey: process.env.FIREBASE_PRIVATE_KEY.replace(/\\n/g, '\n'),
  }),
});

const db = getFirestore(app);

const paymentMethods = [
  { name: 'Espèces', code: 'cash', description: 'Paiement en espèces', isActive: true },
  { name: 'Carte bancaire', code: 'card', description: 'Paiement par carte de crédit/débit', isActive: true },
  { name: 'Chèque', code: 'check', description: 'Paiement par chèque', isActive: true },
  { name: 'Mobile Money', code: 'mobile', description: 'Paiement via mobile money (Orange Money, etc.)', isActive: true },
];

(async () => {
  const existing = await db.collection('paymentMethods').get();
  if (!existing.empty) {
    console.log(`paymentMethods contient déjà ${existing.size} document(s), rien à faire.`);
    process.exit(0);
  }

  const batch = db.batch();
  for (const method of paymentMethods) {
    batch.set(db.collection('paymentMethods').doc(), method);
  }
  await batch.commit();

  console.log(`${paymentMethods.length} modes de paiement créés.`);
  process.exit(0);
})().catch((err) => {
  console.error('Échec du seed :', err.message);
  process.exit(1);
});
