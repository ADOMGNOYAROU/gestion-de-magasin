import ExcelJS from 'exceljs';
import type { AuthUser } from '../auth';
import type { VentesReportData, VentesReportFilters } from './ventes-report';

function fcfa(n: number): string {
  return `${Math.round(n).toLocaleString('fr-FR')} FCFA`;
}

// Reprend l'intention d'app/Exports/VentesExport.php, en corrigeant au passage
// des références de champs erronées dans l'original ($vente->produit,
// $vente->prix_unitaire... n'existent pas sur le modèle Vente actuel — les
// lignes de vente vivent dans VenteProduit/venteProduits, comme le fait
// correctement resources/views/rapports/ventes_pdf.blade.php). On repart donc
// du même modèle de données corrigé que buildVentesReportDoc plutôt que de
// reproduire le bug.
export async function buildVentesReportExcel(
  data: VentesReportData,
  filters: VentesReportFilters,
  authUser: AuthUser,
  dateGeneration: string,
): Promise<Buffer> {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet('Rapport Ventes');

  sheet.addRow(['RAPPORT DE VENTES']).font = { bold: true, size: 16 };
  sheet.addRow([`Période : ${filters.dateDebut} au ${filters.dateFin}`]).font = { italic: true };
  sheet.addRow([`Généré par : ${authUser.role}`]).font = { italic: true };
  sheet.addRow([`Date de génération : ${dateGeneration}`]).font = { italic: true };
  sheet.addRow([]);

  sheet.addRow(['RÉSUMÉ']).font = { bold: true };
  sheet.addRow(['Total des ventes', data.totalVentes]);
  sheet.addRow(["Chiffre d'affaires total", fcfa(data.totalCA)]);
  sheet.addRow(['Bénéfice total', fcfa(data.totalBenefice)]);
  sheet.addRow([]);

  sheet.addRow(['VENTES PAR BOUTIQUE']).font = { bold: true };
  sheet.addRow(['Boutique', 'Magasin', 'Nombre de ventes', 'CA', 'Bénéfice']).font = { bold: true };
  for (const b of data.parBoutique) {
    sheet.addRow([b.boutiqueNom, b.magasinNom, b.ventes, fcfa(b.ca), fcfa(b.benefice)]);
  }
  sheet.addRow([]);

  sheet.addRow(['VENTES PAR PRODUIT']).font = { bold: true };
  sheet.addRow(['Produit', 'Catégorie', 'Quantité vendue', 'CA', 'Bénéfice']).font = { bold: true };
  for (const p of data.parProduit) {
    sheet.addRow([p.produitNom, p.categorie, p.quantite, fcfa(p.ca), fcfa(p.benefice)]);
  }
  sheet.addRow([]);

  sheet.addRow(['DÉTAIL DES VENTES']).font = { bold: true };
  sheet.addRow(['Date', 'Produit', 'Boutique', 'Magasin', 'Quantité', 'Prix unitaire', 'Total', 'Bénéfice']).font = {
    bold: true,
  };
  for (const l of data.lignes) {
    sheet.addRow([
      l.dateVente,
      l.produitNom,
      l.boutiqueNom,
      l.magasinNom,
      l.quantite,
      fcfa(l.prixUnitaire),
      fcfa(l.sousTotal),
      fcfa(l.benefice),
    ]);
  }

  sheet.columns.forEach((col) => {
    col.width = 20;
  });

  const buffer = await workbook.xlsx.writeBuffer();
  return Buffer.from(buffer);
}
