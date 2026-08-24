import type { AuthUser } from '../auth';
import type { Content, DocDefinition } from './pdf';
import type { StockDetailRow, StockReportData } from './stock-report';

type TableCell = NonNullable<NonNullable<Extract<Content, { table: unknown }>['table']>['body']>[number][number];

function statutBadge(quantite: number, seuilAlerte: number): { text: string; color: string } {
  if (quantite === 0) return { text: 'RUPTURE', color: '#dc3545' };
  if (quantite <= seuilAlerte) return { text: 'ALERTE', color: '#856404' };
  return { text: 'OK', color: '#28a745' };
}

function fcfa(n: number): string {
  return `${Math.round(n).toLocaleString('fr-FR')} FCFA`;
}

function detailTable(title: string, stocks: StockDetailRow[], pageBreak?: 'before'): Content[] {
  const body: TableCell[][] = [
    [
      { text: 'Produit', style: 'th' },
      { text: 'Quantité', style: 'th', alignment: 'center' },
      { text: 'Seuil', style: 'th', alignment: 'center' },
      { text: 'Statut', style: 'th', alignment: 'center' },
    ],
    ...stocks.map((s): TableCell[] => {
      const badge = statutBadge(s.quantite, s.seuilAlerte);
      return [
        s.produitNom,
        { text: String(s.quantite), alignment: 'center' },
        { text: String(s.seuilAlerte), alignment: 'center' },
        { text: badge.text, alignment: 'center', color: badge.color, bold: true },
      ];
    }),
  ];

  return [
    { text: title, style: 'h3', ...(pageBreak ? { pageBreak } : {}) },
    {
      table: { headerRows: 1, widths: ['*', 'auto', 'auto', 'auto'], body },
      layout: 'lightHorizontalLines',
      margin: [0, 0, 0, 10],
    },
  ];
}

// Reprend la mise en forme de resources/views/rapports/stock_pdf.blade.php,
// traduite en document pdfmake (tableaux + styles) plutôt qu'en HTML/CSS —
// voir la note sur le choix de pdfmake dans lib/rapports/pdf.ts.
export function buildStockReportDoc(data: StockReportData, authUser: AuthUser, dateGeneration: string): DocDefinition {
  const content: Content[] = [
    { text: 'RAPPORT DE STOCK', style: 'title', alignment: 'center' },
    { text: `Généré le : ${dateGeneration}`, alignment: 'center', style: 'subtitle' },
    { text: `Par : ${authUser.role}`, alignment: 'center', style: 'subtitle' },
  ];

  if (data.scopeNom) {
    content.push({
      text: data.scope.type === 'magasin' ? `Magasin : ${data.scopeNom}` : `Boutique : ${data.scopeNom}`,
      alignment: 'center',
      style: 'subtitle',
    });
  }

  const totalMagasin = data.rows.reduce((s, r) => s + r.stockMagasin, 0);
  const totalBoutique = data.rows.reduce((s, r) => s + r.stockBoutique, 0);
  content.push({
    style: 'infoBox',
    table: {
      widths: ['*'],
      body: [
        [
          {
            text: [
              { text: 'Résumé : ', bold: true },
              `${data.rows.length} produits actifs\n`,
              { text: 'Stock total magasins : ', bold: true },
              `${totalMagasin} unités\n`,
              { text: 'Stock total boutiques : ', bold: true },
              `${totalBoutique} unités`,
            ],
          },
        ],
      ],
    },
    layout: 'noBorders',
    margin: [0, 0, 0, 15],
  });

  const showMagasinCol = data.scope.type !== 'boutique';
  const showBoutiqueCol = data.scope.type !== 'magasin';

  const header: TableCell[] = [
    { text: 'Produit', style: 'th' },
    { text: 'Catégorie', style: 'th' },
    { text: 'Prix Vente', style: 'th', alignment: 'right' },
    ...(showMagasinCol ? [{ text: 'Stock Magasin', style: 'th', alignment: 'center' as const }] : []),
    ...(showBoutiqueCol ? [{ text: 'Stock Boutiques', style: 'th', alignment: 'center' as const }] : []),
    { text: 'Statut', style: 'th', alignment: 'center' },
  ];

  const rows: TableCell[][] = data.rows.map((r): TableCell[] => {
    const badge = statutBadge(
      data.scope.type === 'magasin'
        ? r.stockMagasin
        : data.scope.type === 'boutique'
          ? r.stockBoutique
          : r.stockMagasin + r.stockBoutique,
      r.seuilAlerteRef,
    );
    return [
      { text: r.nom, bold: true },
      r.categorie,
      { text: fcfa(r.prixVente), alignment: 'right' },
      ...(showMagasinCol ? [{ text: String(r.stockMagasin), alignment: 'center' as const }] : []),
      ...(showBoutiqueCol ? [{ text: String(r.stockBoutique), alignment: 'center' as const }] : []),
      { text: badge.text, alignment: 'center', color: badge.color, bold: true },
    ];
  });

  const widths = ['*', 'auto', 'auto', ...(showMagasinCol ? ['auto'] : []), ...(showBoutiqueCol ? ['auto'] : []), 'auto'];
  const emptyRow: TableCell[] = [{ text: 'Aucun produit trouvé', colSpan: widths.length, alignment: 'center' }];

  content.push({
    table: { headerRows: 1, widths, body: [header, ...(rows.length ? rows : [emptyRow])] },
    layout: 'lightHorizontalLines',
    margin: [0, 0, 0, 15],
  });

  if (data.scope.type === 'admin') {
    let first = true;
    for (const [, group] of data.detailParMagasin) {
      content.push(...detailTable(`Magasin — ${group.nom}`, group.stocks, first ? 'before' : undefined));
      first = false;
    }
    for (const [, group] of data.detailParBoutique) {
      content.push(...detailTable(`Boutique — ${group.nom} (${group.magasinNom})`, group.stocks));
    }
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
      h3: { fontSize: 12, bold: true, margin: [0, 10, 0, 5] },
      th: { bold: true, fillColor: '#f2f2f2' },
      infoBox: { fontSize: 9 },
      footer: { fontSize: 8, color: '#666666' },
    },
  };
}
