import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { CashRegisterSessionDoc } from '@/lib/format/cash-register-session';
import { getDb } from '@/lib/firebase-admin';
import { resolveField } from '@/lib/firestore/resolve-field';
import { notifyIfNewlyCritical } from '@/lib/notifications/stock-alert';
import { venteSchema } from '@/lib/schemas/vente';

interface VenteDoc {
  boutiqueId: string;
  userId: string;
  sessionCaisseId: string | null;
  paymentMethodId: string;
  paymentType: 'immediate' | 'credit' | 'mixed';
  clientId: string | null;
  montantTotal: number;
  montantRecu: number;
  monnaie: number;
  numeroTicket: string;
  status: string;
  dateVente: string;
  notes: string | null;
}

interface VenteLigneDoc {
  produitId: string;
  quantite: number;
  prixUnitaire: number;
  remise: number;
  remisePourcentage: number;
  sousTotal: number;
}

interface ProduitDoc {
  nom: string;
  prixVente: number;
}

interface CreditDoc {
  venteId: string;
  clientId: string;
  totalAmount: number;
  remainingBalance: number;
  status: 'active' | 'paid';
  createdAt: string;
}

// Reprend VenteController::index() : 50 dernières ventes, restreintes à sa propre
// boutique pour un vendeur (les gestionnaires/admins voient tout).
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'vendeur');

  const db = getDb();
  let query = db.collection('ventes').orderBy('dateVente', 'desc').limit(50) as FirebaseFirestore.Query;
  if (authUser.role === 'vendeur' && authUser.boutiqueId) {
    query = db
      .collection('ventes')
      .where('boutiqueId', '==', authUser.boutiqueId)
      .orderBy('dateVente', 'desc')
      .limit(50);
  }

  const snapshot = await query.get();

  const ventes = await Promise.all(
    snapshot.docs.map(async (doc) => {
      const data = doc.data() as VenteDoc;
      const [boutiqueNom, modePaiement, clientNom, lignesSnapshot] = await Promise.all([
        resolveField('boutiques', data.boutiqueId, 'nom'),
        resolveField('paymentMethods', data.paymentMethodId, 'name'),
        resolveField('clients', data.clientId, 'nom'),
        doc.ref.collection('produits').get(),
      ]);

      const lignes = await Promise.all(
        lignesSnapshot.docs.map(async (ligneDoc) => {
          const ligne = ligneDoc.data() as VenteLigneDoc;
          const produitNom = await resolveField('produits', ligne.produitId, 'nom');
          return {
            produit: produitNom,
            quantite: ligne.quantite,
            prix_unitaire: ligne.prixUnitaire,
            remise: ligne.remise,
            sous_total: ligne.sousTotal,
          };
        }),
      );

      return {
        id: doc.id,
        numero_ticket: data.numeroTicket,
        boutique: boutiqueNom,
        client: clientNom,
        payment_type: data.paymentType ?? 'immediate',
        montant_total: data.montantTotal,
        montant_recu: data.montantRecu,
        monnaie: data.monnaie,
        mode_paiement: modePaiement,
        status: data.status,
        date_vente: data.dateVente,
        lignes,
      };
    }),
  );

  return Response.json(ventes);
});

function generateTicketPrefix(): string {
  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, '0');
  const d = String(now.getDate()).padStart(2, '0');
  return `TKT-${y}${m}${d}-`;
}

// Reprend VenteController::store() : le checkout POS, plus le paiement à
// crédit/mixte (centro : VenteController web + CreditController).
//
// Le stock n'est décrémenté qu'à hauteur de la fraction réellement payée
// (100% en immédiat, 0% en crédit pur, proportionnel en mixte) — la
// marchandise est considérée "réservée" tant qu'elle n'est pas payée, et se
// décrémente au fil des paiements de crédit (voir POST /api/credits/[id]/paiements).
// C'est une réconciliation délibérée de deux comportements incohérents
// trouvés dans centro : son VenteController web décrémente 100% du stock à la
// vente ET son CreditController décrémente à nouveau proportionnellement à
// chaque paiement, ce qui double-compte pour toute vente à crédit ou mixte.
//
// Reproduit fidèlement un comportement existant : aucune vérification de
// disponibilité de stock au checkout (contrairement aux transferts).
export const POST = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'vendeur');
  const body = venteSchema.parse(await request.json());

  const boutiqueId = body.boutiqueId ?? authUser.boutiqueId;
  if (!boutiqueId) {
    throw new ApiError(422, 'Aucune boutique associée à cet utilisateur.');
  }

  const db = getDb();
  const boutiqueRef = db.collection('boutiques').doc(boutiqueId);
  const venteRef = db.collection('ventes').doc();
  const creditRef = db.collection('credits').doc();
  const ticketPrefix = generateTicketPrefix();

  const result = await db.runTransaction(async (tx) => {
    // --- Lecture (tout doit être lu avant la moindre écriture dans une transaction Firestore) ---
    const boutiqueSnap = await tx.get(boutiqueRef);
    if (!boutiqueSnap.exists) {
      throw new ApiError(422, 'Boutique introuvable.');
    }
    const magasinId = (boutiqueSnap.data() as { magasinId: string | null }).magasinId ?? null;

    const clientRef = body.clientId ? db.collection('clients').doc(body.clientId) : null;
    const clientSnap = clientRef ? await tx.get(clientRef) : null;
    if (clientRef && !clientSnap?.exists) {
      throw new ApiError(422, 'Client introuvable.');
    }

    const openSessionSnap = await tx.get(
      db
        .collection('cashRegisterSessions')
        .where('vendeurId', '==', authUser.uid)
        .where('status', '==', 'ouverte')
        .limit(1),
    );
    const sessionDoc = openSessionSnap.empty ? null : openSessionSnap.docs[0];

    const sessionVentesSnap = sessionDoc
      ? await tx.get(db.collection('ventes').where('sessionCaisseId', '==', sessionDoc.id))
      : null;

    const produitRefs = body.lignes.map((l) => db.collection('produits').doc(l.produitId));
    const stockRefs = body.lignes.map((l) => db.collection('stocksBoutique').doc(`${boutiqueId}_${l.produitId}`));
    const produitSnaps = await Promise.all(produitRefs.map((ref) => tx.get(ref)));
    const stockSnaps = await Promise.all(stockRefs.map((ref) => tx.get(ref)));

    const ticketSnap = await tx.get(
      db
        .collection('ventes')
        .where('numeroTicket', '>=', ticketPrefix)
        .where('numeroTicket', '<', ticketPrefix + '')
        .orderBy('numeroTicket', 'desc')
        .limit(1),
    );

    // --- Calculs ---
    let numeroSequence = 1;
    if (!ticketSnap.empty) {
      const lastTicket = (ticketSnap.docs[0].data() as VenteDoc).numeroTicket;
      const parts = lastTicket.split('-');
      if (parts.length >= 3) numeroSequence = parseInt(parts[2], 10) + 1;
    }
    const numeroTicket = `${ticketPrefix}${String(numeroSequence).padStart(4, '0')}`;

    let montantTotal = 0;
    const lignes = body.lignes.map((ligne, i) => {
      const produitSnap = produitSnaps[i];
      if (!produitSnap.exists) {
        throw new ApiError(422, `Produit introuvable : ${ligne.produitId}`);
      }
      const produit = produitSnap.data() as ProduitDoc;
      const prixUnitaire = produit.prixVente;
      const remise = ligne.remise ?? 0;
      const remisePourcentage = prixUnitaire > 0 ? (remise / prixUnitaire) * 100 : 0;
      const sousTotal = (prixUnitaire - remise) * ligne.quantite;
      montantTotal += sousTotal;
      return { produitId: ligne.produitId, quantite: ligne.quantite, prixUnitaire, remise, remisePourcentage, sousTotal };
    });

    if (body.paymentType === 'mixed' && body.montantRecu > montantTotal) {
      throw new ApiError(422, 'Le montant reçu ne peut pas dépasser le total de la vente.');
    }

    const montantRecu = body.paymentType === 'credit' ? 0 : body.montantRecu;
    const monnaie = body.paymentType === 'immediate' ? montantRecu - montantTotal : 0;

    const fractionPayee = body.paymentType === 'immediate' ? 1 : body.paymentType === 'credit' ? 0 : montantTotal > 0 ? montantRecu / montantTotal : 0;

    // --- Écritures ---
    const vente: VenteDoc = {
      boutiqueId,
      userId: authUser.uid,
      sessionCaisseId: sessionDoc?.id ?? null,
      paymentMethodId: body.paymentMethodId,
      paymentType: body.paymentType,
      clientId: body.clientId ?? null,
      montantTotal,
      montantRecu,
      monnaie,
      numeroTicket,
      status: 'terminee',
      dateVente: new Date().toISOString().slice(0, 10),
      notes: null,
    };
    tx.set(venteRef, vente);

    lignes.forEach((ligne) => {
      tx.set(venteRef.collection('produits').doc(), ligne as VenteLigneDoc);
    });

    let creditCree = false;
    if (body.paymentType === 'credit' || (body.paymentType === 'mixed' && montantRecu < montantTotal)) {
      const credit: CreditDoc = {
        venteId: venteRef.id,
        clientId: body.clientId!,
        totalAmount: montantTotal,
        remainingBalance: montantTotal - montantRecu,
        status: 'active',
        createdAt: new Date().toISOString(),
      };
      tx.set(creditRef, credit);
      creditCree = true;
    }

    const alertesAVerifier: {
      produitId: string;
      produitNom: string;
      quantiteAvant: number;
      seuilAlerte: number;
    }[] = [];
    stockSnaps.forEach((stockSnap, i) => {
      if (stockSnap.exists) {
        const decrement = Math.round(fractionPayee * body.lignes[i].quantite);
        if (decrement <= 0) return;
        const stock = stockSnap.data() as { quantite: number; seuilAlerte: number };
        tx.update(stockRefs[i], { quantite: stock.quantite - decrement });
        alertesAVerifier.push({
          produitId: body.lignes[i].produitId,
          produitNom: (produitSnaps[i].data() as ProduitDoc).nom,
          quantiteAvant: stock.quantite,
          seuilAlerte: stock.seuilAlerte,
        });
      }
    });

    if (sessionDoc && sessionVentesSnap) {
      const session = sessionDoc.data() as CashRegisterSessionDoc;
      const totalVentesExistantes = sessionVentesSnap.docs.reduce(
        (sum, d) => sum + ((d.data() as VenteDoc).montantTotal ?? 0),
        0,
      );
      // Le montant théorique en caisse ne doit refléter que l'argent réellement encaissé.
      const montantTheorique = session.montantInitial + totalVentesExistantes + montantRecu;
      tx.update(sessionDoc.ref, { montantTheorique });
    }

    return {
      id: venteRef.id,
      numero_ticket: numeroTicket,
      montant_total: montantTotal,
      monnaie,
      credit_id: creditCree ? creditRef.id : null,
      magasinId,
      alertesAVerifier,
      fractionPayee,
    };
  });

  // Best-effort : ne doit jamais faire échouer une vente déjà validée.
  try {
    const boutiqueNom = (await resolveField('boutiques', boutiqueId, 'nom')) ?? boutiqueId;
    for (const ligne of result.alertesAVerifier) {
      const ligneOriginale = body.lignes.find((l) => l.produitId === ligne.produitId);
      const quantiteVendue = Math.round(result.fractionPayee * (ligneOriginale?.quantite ?? 0));
      await notifyIfNewlyCritical({
        produitId: ligne.produitId,
        produitNom: ligne.produitNom,
        site: boutiqueNom,
        quantiteAvant: ligne.quantiteAvant,
        quantiteApres: ligne.quantiteAvant - quantiteVendue,
        seuilAlerte: ligne.seuilAlerte,
        magasinId: result.magasinId,
      });
    }
  } catch (error) {
    console.error('notifyIfNewlyCritical a échoué (vente déjà validée, non bloquant) :', error);
  }

  const { magasinId: _magasinId, alertesAVerifier: _alertes, fractionPayee: _fraction, ...response } = result;
  return Response.json(response, { status: 201 });
});
