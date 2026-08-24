import type { AuthUser } from '../auth';
import type { Content, DocDefinition } from './pdf';
import type { VentesReportData, VentesReportFilters } from './ventes-report';

type TableCell = NonNullable<NonNullable<Extract<Content, { table: unknown }>['table']>['body']>[number][number];

function fcfa(n: number): string {
  return `${Math.round(n).toLocaleString('fr-FR')} FCFA`;
}

function marge(ca: number, benefice: number): string {
  return ca > 0 ? `${Math.round((benefice / ca) * 1000) / 10}%` : '0%';
}

// Reprend la mise en forme de resources/views/rapports/ventes_pdf.blade.php.
export function buildVentesReportDoc(
  data: VentesReportData,
  filters: VentesReportFilters,
  authUser: AuthUser,
  dateGeneration: string,
): DocDefinition {
  const content: Content[] = [
    { text: 'RAPPORT DE VENTES', style: 'title', alignment: 'center' },
    { text: `Période : ${filters.dateDebut} au ${filters.dateFin}`, alignment: 'center', style: 'subtitle' },
    { text: `Généré le : ${dateGeneration}`, alignment: 'center', style: 'subtitle' },
    { text: `Par : ${authUser.role}`, alignment: 'center', style: 'subtitle' },
  ];

  content.push({
    style: 'summaryBox',
    table: {
      widths: ['*', 'auto'],
      body: [
        ['Total des ventes', { text: String(data.totalVentes), alignment: 'right' }],
        ["Chiffre d'affaires total", { text: fcfa(data.totalCA), alignment: 'right' }],
        ['Bénéfice total', { text: fcfa(data.totalBenefice), alignment: 'right' }],
        ['Marge bénéficiaire', { text: marge(data.totalCA, data.totalBenefice), alignment: 'right' }],
      ] as TableCell[][],
    },
    layout: 'noBorders',
    margin: [0, 5, 0, 15],
  });

  content.push({ text: 'VENTES PAR BOUTIQUE', style: 'sectionTitle' });
  const boutiqueBody: TableCell[][] = [
    [
      { text: 'Boutique', style: 'th' },
      { text: 'Magasin', style: 'th' },
      { text: 'Nb ventes', style: 'th', alignment: 'center' },
      { text: 'CA', style: 'th', alignment: 'right' },
      { text: 'Bénéfice', style: 'th', alignment: 'right' },
      { text: 'Marge', style: 'th', alignment: 'right' },
    ],
    ...(data.parBoutique.length
      ? data.parBoutique.map(
          (b): TableCell[] => [
            { text: b.boutiqueNom, bold: true },
            b.magasinNom,
            { text: String(b.ventes), alignment: 'center' },
            { text: fcfa(b.ca), alignment: 'right' },
            { text: fcfa(b.benefice), alignment: 'right' },
            { text: marge(b.ca, b.benefice), alignment: 'right' },
          ],
        )
      : [[{ text: 'Aucune vente trouvée', colSpan: 6, alignment: 'center' as const }]]),
  ];
  content.push({
    table: { headerRows: 1, widths: ['*', '*', 'auto', 'auto', 'auto', 'auto'], body: boutiqueBody },
    layout: 'lightHorizontalLines',
    margin: [0, 0, 0, 15],
  });

  content.push({ text: 'VENTES PAR PRODUIT', style: 'sectionTitle', pageBreak: 'before' });
  const produitBody: TableCell[][] = [
    [
      { text: 'Produit', style: 'th' },
      { text: 'Catégorie', style: 'th' },
      { text: 'Quantité', style: 'th', alignment: 'center' },
      { text: 'CA', style: 'th', alignment: 'right' },
      { text: 'Bénéfice', style: 'th', alignment: 'right' },
      { text: 'Marge', style: 'th', alignment: 'right' },
    ],
    ...(data.parProduit.length
      ? data.parProduit.map(
          (p): TableCell[] => [
            { text: p.produitNom, bold: true },
            p.categorie,
            { text: String(p.quantite), alignment: 'center' },
            { text: fcfa(p.ca), alignment: 'right' },
            { text: fcfa(p.benefice), alignment: 'right' },
            { text: marge(p.ca, p.benefice), alignment: 'right' },
          ],
        )
      : [[{ text: 'Aucune vente trouvée', colSpan: 6, alignment: 'center' as const }]]),
  ];
  content.push({
    table: { headerRows: 1, widths: ['*', '*', 'auto', 'auto', 'auto', 'auto'], body: produitBody },
    layout: 'lightHorizontalLines',
    margin: [0, 0, 0, 15],
  });

  content.push({ text: 'DÉTAIL DES VENTES', style: 'sectionTitle', pageBreak: 'before' });
  const detailBody: TableCell[][] = [
    [
      { text: 'Date', style: 'th' },
      { text: 'Produit', style: 'th' },
      { text: 'Boutique', style: 'th' },
      { text: 'Qté', style: 'th', alignment: 'center' },
      { text: 'Prix unitaire', style: 'th', alignment: 'right' },
      { text: 'Total', style: 'th', alignment: 'right' },
      { text: 'Bénéfice', style: 'th', alignment: 'right' },
    ],
    ...(data.lignes.length
      ? data.lignes.map(
          (l): TableCell[] => [
            l.dateVente,
            l.produitNom,
            l.boutiqueNom,
            { text: String(l.quantite), alignment: 'center' },
            { text: fcfa(l.prixUnitaire), alignment: 'right' },
            { text: fcfa(l.sousTotal), alignment: 'right' },
            { text: fcfa(l.benefice), alignment: 'right' },
          ],
        )
      : [[{ text: 'Aucune vente trouvée', colSpan: 7, alignment: 'center' as const }]]),
  ];
  content.push({
    table: { headerRows: 1, widths: ['auto', '*', '*', 'auto', 'auto', 'auto', 'auto'], body: detailBody },
    layout: 'lightHorizontalLines',
    margin: [0, 0, 0, 10],
  });

  if (data.lignes.length > 0) {
    content.push({
      table: {
        widths: ['*', 'auto', 'auto'],
        body: [
          [
            { text: 'TOTAUX', bold: true, alignment: 'right' },
            { text: fcfa(data.totalCA), bold: true, alignment: 'right' },
            { text: fcfa(data.totalBenefice), bold: true, alignment: 'right' },
          ],
        ] as TableCell[][],
      },
      layout: 'headerLineOnly',
      margin: [0, 0, 0, 15],
    });
  }

  content.push({
    text: `Rapport généré automatiquement par le système de gestion de stock — ${dateGeneration}`,
    style: 'footer',
    alignment: 'center',
  });

  return {
    pageOrientation: 'landscape',
    content,
    styles: {
      title: { fontSize: 18, bold: true },
      subtitle: { fontSize: 9, color: '#666666' },
      sectionTitle: { fontSize: 12, bold: true, color: '#ffffff', fillColor: '#007bff', margin: [0, 10, 0, 5] },
      summaryBox: { fontSize: 10 },
      th: { bold: true, fillColor: '#f2f2f2' },
      footer: { fontSize: 8, color: '#666666', margin: [0, 20, 0, 0] },
    },
  };
}
