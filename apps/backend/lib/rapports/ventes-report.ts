import type { AuthUser } from '../auth';
import { getDb } from '../firebase-admin';

interface VenteDoc {
  boutiqueId: string;
  montantTotal: number;
  dateVente: string;
}

interface VenteLigneDoc {
  produitId: string;
  quantite: number;
  prixUnitaire: number;
  remise: number;
  sousTotal: number;
}

interface ProduitDoc {
  nom: string;
  categorie: string;
  prixAchat: number;
}

interface BoutiqueDoc {
  nom: string;
  magasinId: string;
}

export interface VenteLigneRow {
  dateVente: string;
  produitId: string;
  produitNom: string;
  categorie: string;
  boutiqueId: string;
  boutiqueNom: string;
  magasinNom: string;
  quantite: number;
  prixUnitaire: number;
  sousTotal: number;
  benefice: number;
}

export interface VentesReportFilters {
  dateDebut: string;
  dateFin: string;
  magasinId?: string | null;
  boutiqueId?: string | null;
}

export interface VentesReportData {
  lignes: VenteLigneRow[];
  totalVentes: number;
  totalCA: number;
  totalBenefice: number;
  parBoutique: { boutiqueNom: string; magasinNom: string; ventes: number; ca: number; benefice: number }[];
  parProduit: { produitNom: string; categorie: string; quantite: number; ca: number; benefice: number }[];
}

// Reprend RapportController::getVentesData(). Le bénéfice par ligne inclut la
// remise (comme Vente::getBeneficeTotalAttribute()) — contrairement à
// ventes_pdf.blade.php qui, dans la colonne détail, omet la remise dans son
// calcul ; on standardise sur la formule complète plutôt que de reproduire
// cette incohérence entre deux formules légèrement différentes du code d'origine.
//
// La branche vendeur du contrôleur Laravel n'est jamais atteignable (la route
// est restreinte au middleware 'gestionnaire' dans routes/web.php) : non reprise.
export async function buildVentesReportData(authUser: AuthUser, filters: VentesReportFilters): Promise<VentesReportData> {
  const db = getDb();

  const ventesSnap = await db
    .collection('ventes')
    .where('dateVente', '>=', filters.dateDebut)
    .where('dateVente', '<=', filters.dateFin)
    .get();

  let ventes = ventesSnap.docs.map((doc) => ({ id: doc.id, ref: doc.ref, data: doc.data() as VenteDoc }));

  const boutiquesSnap = await db.collection('boutiques').get();
  const boutiquesById = new Map(boutiquesSnap.docs.map((d) => [d.id, d.data() as BoutiqueDoc]));

  if (authUser.role === 'gestionnaire' && authUser.magasinId) {
    const boutiqueIdsDuMagasin = new Set(
      boutiquesSnap.docs.filter((d) => (d.data() as BoutiqueDoc).magasinId === authUser.magasinId).map((d) => d.id),
    );
    ventes = ventes.filter((v) => boutiqueIdsDuMagasin.has(v.data.boutiqueId));
  }
  if (filters.magasinId) {
    ventes = ventes.filter((v) => boutiquesById.get(v.data.boutiqueId)?.magasinId === filters.magasinId);
  }
  if (filters.boutiqueId) {
    ventes = ventes.filter((v) => v.data.boutiqueId === filters.boutiqueId);
  }

  const magasinsSnap = await db.collection('magasins').get();
  const magasinsById = new Map(magasinsSnap.docs.map((d) => [d.id, (d.data() as { nom: string }).nom]));

  const lignesParVente = await Promise.all(ventes.map((v) => v.ref.collection('produits').get()));
  const produitIds = new Set<string>();
  lignesParVente.forEach((snap) => snap.docs.forEach((d) => produitIds.add((d.data() as VenteLigneDoc).produitId)));

  const produits = new Map<string, ProduitDoc>();
  await Promise.all(
    [...produitIds].map(async (id) => {
      const doc = await db.collection('produits').doc(id).get();
      if (doc.exists) produits.set(id, doc.data() as ProduitDoc);
    }),
  );

  const lignes: VenteLigneRow[] = [];
  ventes.forEach((v, i) => {
    const boutique = boutiquesById.get(v.data.boutiqueId);
    const boutiqueNom = boutique?.nom ?? v.data.boutiqueId;
    const magasinNom = boutique ? (magasinsById.get(boutique.magasinId) ?? '') : '';

    lignesParVente[i].docs.forEach((ligneDoc) => {
      const ligne = ligneDoc.data() as VenteLigneDoc;
      const produit = produits.get(ligne.produitId);
      const benefice = (ligne.prixUnitaire - (produit?.prixAchat ?? 0)) * ligne.quantite - ligne.remise;
      lignes.push({
        dateVente: v.data.dateVente,
        produitId: ligne.produitId,
        produitNom: produit?.nom ?? ligne.produitId,
        categorie: produit?.categorie ?? '',
        boutiqueId: v.data.boutiqueId,
        boutiqueNom,
        magasinNom,
        quantite: ligne.quantite,
        prixUnitaire: ligne.prixUnitaire,
        sousTotal: ligne.sousTotal,
        benefice,
      });
    });
  });

  const totalVentes = ventes.length;
  const totalCA = ventes.reduce((sum, v) => sum + v.data.montantTotal, 0);
  const totalBenefice = lignes.reduce((sum, l) => sum + l.benefice, 0);

  const parBoutiqueMap = new Map<string, { boutiqueNom: string; magasinNom: string; ventes: number; ca: number; benefice: number }>();
  ventes.forEach((v) => {
    const boutique = boutiquesById.get(v.data.boutiqueId);
    const key = v.data.boutiqueId;
    if (!parBoutiqueMap.has(key)) {
      parBoutiqueMap.set(key, {
        boutiqueNom: boutique?.nom ?? key,
        magasinNom: boutique ? (magasinsById.get(boutique.magasinId) ?? '') : '',
        ventes: 0,
        ca: 0,
        benefice: 0,
      });
    }
    const entry = parBoutiqueMap.get(key)!;
    entry.ventes += 1;
    entry.ca += v.data.montantTotal;
  });
  lignes.forEach((l) => {
    const entry = parBoutiqueMap.get(l.boutiqueId);
    if (entry) entry.benefice += l.benefice;
  });

  const parProduitMap = new Map<string, { produitNom: string; categorie: string; quantite: number; ca: number; benefice: number }>();
  lignes.forEach((l) => {
    if (!parProduitMap.has(l.produitId)) {
      parProduitMap.set(l.produitId, { produitNom: l.produitNom, categorie: l.categorie, quantite: 0, ca: 0, benefice: 0 });
    }
    const entry = parProduitMap.get(l.produitId)!;
    entry.quantite += l.quantite;
    entry.ca += l.sousTotal;
    entry.benefice += l.benefice;
  });

  return {
    lignes: lignes.sort((a, b) => a.dateVente.localeCompare(b.dateVente)),
    totalVentes,
    totalCA,
    totalBenefice,
    parBoutique: [...parBoutiqueMap.values()],
    parProduit: [...parProduitMap.values()],
  };
}
