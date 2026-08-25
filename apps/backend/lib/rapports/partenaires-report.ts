import type { AuthUser } from '../auth';
import { getDb } from '../firebase-admin';

interface PartenaireDoc {
  nom: string;
  telephone: string | null;
  email: string | null;
}

interface EntreeStockDoc {
  produitId: string;
  magasinId: string;
  partenaireId: string | null;
  quantite: number;
  prixUnitaire: number;
  montantTotal: number;
  dateEntree: string;
}

interface ProduitDoc {
  nom: string;
  categorie: string;
}

export interface PartenaireAchatRow {
  dateEntree: string;
  produitNom: string;
  categorie: string;
  quantite: number;
  prixUnitaire: number;
  montantTotal: number;
  magasinNom: string;
}

export interface PartenaireReportEntry {
  id: string;
  nom: string;
  telephone: string | null;
  email: string | null;
  achats: PartenaireAchatRow[];
  produitsAchetes: { produitNom: string; quantite: number; montantTotal: number }[];
  totalAchats: number;
  totalMontant: number;
}

export interface PartenairesReportData {
  scopeMagasinNom: string | null;
  partenaires: PartenaireReportEntry[];
}

// Reprend RapportController::rapportPartenairesPDF(). Le contrôleur original
// gère aussi un cas "vendeur → pas d'accès", inatteignable ici puisque cette
// route exige déjà le rôle gestionnaire au minimum (cf. authenticateWithRole).
export async function buildPartenairesReportData(authUser: AuthUser): Promise<PartenairesReportData> {
  const db = getDb();

  const [partenairesSnap, entreesSnap, magasinsSnap] = await Promise.all([
    db.collection('partenaires').orderBy('nom').get(),
    authUser.role === 'gestionnaire' && authUser.magasinId
      ? db.collection('entreesStock').where('magasinId', '==', authUser.magasinId).get()
      : db.collection('entreesStock').get(),
    db.collection('magasins').get(),
  ]);

  const magasinsById = new Map(magasinsSnap.docs.map((d) => [d.id, (d.data() as { nom: string }).nom]));
  const entrees = entreesSnap.docs.map((d) => d.data() as EntreeStockDoc).filter((e) => e.partenaireId);

  const produitIds = [...new Set(entrees.map((e) => e.produitId))];
  const produits = new Map<string, ProduitDoc>();
  await Promise.all(
    produitIds.map(async (id) => {
      const doc = await db.collection('produits').doc(id).get();
      if (doc.exists) produits.set(id, doc.data() as ProduitDoc);
    }),
  );

  let scopeMagasinNom: string | null = null;
  if (authUser.role === 'gestionnaire' && authUser.magasinId) {
    scopeMagasinNom = magasinsById.get(authUser.magasinId) ?? null;
  }

  const partenaires: PartenaireReportEntry[] = [];
  for (const doc of partenairesSnap.docs) {
    const data = doc.data() as PartenaireDoc;
    const entreesDuPartenaire = entrees.filter((e) => e.partenaireId === doc.id);
    if (authUser.role === 'gestionnaire' && entreesDuPartenaire.length === 0) {
      // Comme whereHas() côté Laravel : un gestionnaire ne voit que les
      // partenaires ayant au moins un achat dans son magasin.
      continue;
    }

    const achats: PartenaireAchatRow[] = entreesDuPartenaire.map((e) => ({
      dateEntree: e.dateEntree,
      produitNom: produits.get(e.produitId)?.nom ?? e.produitId,
      categorie: produits.get(e.produitId)?.categorie ?? '',
      quantite: e.quantite,
      prixUnitaire: e.prixUnitaire,
      montantTotal: e.montantTotal,
      magasinNom: magasinsById.get(e.magasinId) ?? e.magasinId,
    }));

    const parProduit = new Map<string, { produitNom: string; quantite: number; montantTotal: number }>();
    for (const a of achats) {
      if (!parProduit.has(a.produitNom)) parProduit.set(a.produitNom, { produitNom: a.produitNom, quantite: 0, montantTotal: 0 });
      const entry = parProduit.get(a.produitNom)!;
      entry.quantite += a.quantite;
      entry.montantTotal += a.montantTotal;
    }

    partenaires.push({
      id: doc.id,
      nom: data.nom,
      telephone: data.telephone,
      email: data.email,
      achats: achats.sort((a, b) => a.dateEntree.localeCompare(b.dateEntree)),
      produitsAchetes: [...parProduit.values()],
      totalAchats: achats.length,
      totalMontant: achats.reduce((sum, a) => sum + a.montantTotal, 0),
    });
  }

  return { scopeMagasinNom, partenaires };
}
