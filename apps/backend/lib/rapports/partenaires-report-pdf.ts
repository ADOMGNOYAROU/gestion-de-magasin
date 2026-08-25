import type { AuthUser } from '../auth';
import type { Content, DocDefinition } from './pdf';
import type { PartenairesReportData } from './partenaires-report';

type TableCell = NonNullable<NonNullable<Extract<Content, { table: unknown }>['table']>['body']>[number][number];

function fcfa(n: number): string {
  return `${Math.round(n).toLocaleString('fr-FR')} FCFA`;
}

// Reprend la mise en forme de resources/views/rapports/partenaires_pdf.blade.php.
export function buildPartenairesReportDoc(data: PartenairesReportData, authUser: AuthUser, dateGeneration: string): DocDefinition {
  const content: Content[] = [
    { text: 'RAPPORT DES PARTENAIRES', style: 'title', alignment: 'center' },
    { text: `Généré le : ${dateGeneration}`, alignment: 'center', style: 'subtitle' },
    { text: `Par : ${authUser.role}`, alignment: 'center', style: 'subtitle' },
  ];
  if (data.scopeMagasinNom) {
    content.push({ text: `Magasin : ${data.scopeMagasinNom}`, alignment: 'center', style: 'subtitle' });
  }

  const totalAchats = data.partenaires.reduce((s, p) => s + p.totalAchats, 0);
  const totalMontant = data.partenaires.reduce((s, p) => s + p.totalMontant, 0);
  content.push({
    style: 'infoBox',
    table: {
      widths: ['*'],
      body: [
        [
          {
            text: [
              { text: 'Résumé : ', bold: true },
              `${data.partenaires.length} partenaires\n`,
              { text: 'Total des achats : ', bold: true },
              `${fcfa(totalMontant)}\n`,
              { text: "Nombre total d'entrées : ", bold: true },
              String(totalAchats),
            ],
          },
        ],
      ],
    },
    layout: 'noBorders',
    margin: [0, 0, 0, 15],
  });

  if (data.partenaires.length === 0) {
    content.push({ text: 'Aucun partenaire trouvé', alignment: 'center' });
  }

  for (const partenaire of data.partenaires) {
    content.push({ text: partenaire.nom, style: 'partnerName' });
    const contactParts = [partenaire.telephone, partenaire.email].filter(Boolean);
    if (contactParts.length) {
      content.push({ text: contactParts.join(' — '), style: 'contactInfo' });
    }

    const achatsBody: TableCell[][] = [
      [
        { text: 'Date', style: 'th' },
        { text: 'Produit', style: 'th' },
        { text: 'Catégorie', style: 'th' },
        { text: 'Qté', style: 'th', alignment: 'center' },
        { text: 'Prix unitaire', style: 'th', alignment: 'right' },
        { text: 'Total', style: 'th', alignment: 'right' },
        { text: 'Magasin', style: 'th' },
      ],
      ...(partenaire.achats.length
        ? partenaire.achats.map(
            (a): TableCell[] => [
              a.dateEntree,
              a.produitNom,
              a.categorie,
              { text: String(a.quantite), alignment: 'center' },
              { text: fcfa(a.prixUnitaire), alignment: 'right' },
              { text: fcfa(a.montantTotal), alignment: 'right' },
              a.magasinNom,
            ],
          )
        : [[{ text: 'Aucun achat trouvé', colSpan: 7, alignment: 'center' as const }]]),
    ];
    if (partenaire.achats.length) {
      achatsBody.push([
        { text: 'TOTAUX', bold: true, colSpan: 3, alignment: 'right' },
        {},
        {},
        { text: String(partenaire.totalAchats), bold: true, alignment: 'center' },
        '-',
        { text: fcfa(partenaire.totalMontant), bold: true, alignment: 'right' },
        '-',
      ]);
    }
    content.push({
      table: { headerRows: 1, widths: ['auto', '*', 'auto', 'auto', 'auto', 'auto', '*'], body: achatsBody },
      layout: 'lightHorizontalLines',
      margin: [0, 0, 0, 10],
    });
  }

  if (data.partenaires.length > 0) {
    content.push({ text: 'RÉSUMÉ GÉNÉRAL', style: 'sectionTitle', pageBreak: 'before' });
    const sorted = [...data.partenaires].sort((a, b) => b.totalMontant - a.totalMontant);
    const body: TableCell[][] = [
      [
        { text: 'Partenaire', style: 'th' },
        { text: "Nombre d'achats", style: 'th', alignment: 'center' },
        { text: 'Total dépensé', style: 'th', alignment: 'right' },
        { text: 'Moyenne par achat', style: 'th', alignment: 'right' },
      ],
      ...sorted.map(
        (p): TableCell[] => [
          { text: p.nom, bold: true },
          { text: String(p.totalAchats), alignment: 'center' },
          { text: fcfa(p.totalMontant), alignment: 'right' },
          { text: p.totalAchats > 0 ? fcfa(p.totalMontant / p.totalAchats) : '0 FCFA', alignment: 'right' },
        ],
      ),
      [
        { text: 'TOTAUX', bold: true },
        { text: String(totalAchats), bold: true, alignment: 'center' },
        { text: fcfa(totalMontant), bold: true, alignment: 'right' },
        { text: totalAchats > 0 ? fcfa(totalMontant / totalAchats) : '0 FCFA', bold: true, alignment: 'right' },
      ],
    ];
    content.push({
      table: { headerRows: 1, widths: ['*', 'auto', 'auto', 'auto'], body },
      layout: 'lightHorizontalLines',
    });
  }

  content.push({
    text: `Rapport généré automatiquement par le système de gestion de stock — ${dateGeneration}`,
    style: 'footer',
    alignment: 'center',
    margin: [0, 20, 0, 0],
  });

  return {
    content,
    styles: {
      title: { fontSize: 18, bold: true },
      subtitle: { fontSize: 9, color: '#666666' },
      sectionTitle: { fontSize: 12, bold: true, margin: [0, 10, 0, 5] },
      partnerName: { fontSize: 13, bold: true, color: '#007bff', margin: [0, 12, 0, 2] },
      contactInfo: { fontSize: 9, color: '#856404', margin: [0, 0, 0, 5] },
      th: { bold: true, fillColor: '#f2f2f2' },
      infoBox: { fontSize: 9 },
      footer: { fontSize: 8, color: '#666666' },
    },
  };
}
