import { getDb } from '../firebase-admin';

interface NotifyIfNewlyCriticalParams {
  produitId: string;
  produitNom: string;
  site: string;
  quantiteAvant: number;
  quantiteApres: number;
  seuilAlerte: number;
  magasinId?: string | null;
}

// Reprend App\Services\StockAlertNotifier::notifyIfNewlyCritical().
//
// Volontairement appelé APRÈS que la transaction Firestore principale (transfert
// ou vente) ait déjà validé — Firestore ne permet pas de lire une requête
// (recherche des admins/gestionnaires à notifier) après avoir déjà lu des documents
// par référence dans la même transaction sans complexifier fortement le code pour
// un effet secondaire non critique (l'échec d'une notification ne doit jamais faire
// échouer une vente ou un transfert). D'où le try/catch côté appelant.
export async function notifyIfNewlyCritical({
  produitId,
  produitNom,
  site,
  quantiteAvant,
  quantiteApres,
  seuilAlerte,
  magasinId,
}: NotifyIfNewlyCriticalParams): Promise<void> {
  const etaitEnAlerte = quantiteAvant <= seuilAlerte;
  const estEnAlerte = quantiteApres <= seuilAlerte;
  if (etaitEnAlerte || !estEnAlerte) return;

  const db = getDb();
  const [adminsSnap, gestionnairesSnap] = await Promise.all([
    db.collection('users').where('role', '==', 'admin').get(),
    magasinId
      ? db.collection('users').where('role', '==', 'gestionnaire').where('magasinId', '==', magasinId).get()
      : Promise.resolve(null),
  ]);

  const recipientIds = new Set<string>(adminsSnap.docs.map((d) => d.id));
  gestionnairesSnap?.docs.forEach((d) => recipientIds.add(d.id));
  if (recipientIds.size === 0) return;

  const message = `${produitNom} — ${site} : il ne reste que ${quantiteApres} unité(s) (seuil : ${seuilAlerte}).`;
  const createdAt = new Date().toISOString();

  const batch = db.batch();
  for (const userId of recipientIds) {
    batch.set(db.collection('notifications').doc(), {
      userId,
      data: { title: 'Stock critique', message, produitId, site, quantite: quantiteApres, seuilAlerte },
      readAt: null,
      createdAt,
    });
  }
  await batch.commit();
}
