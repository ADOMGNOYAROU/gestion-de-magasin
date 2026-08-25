import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { generatePdf, pdfResponse } from '@/lib/rapports/pdf';
import { buildStockReportData } from '@/lib/rapports/stock-report';
import { buildStockReportDoc } from '@/lib/rapports/stock-report-pdf';

// Reprend RapportController::rapportStockPDF(). GET plutôt que la route web
// d'origine (téléchargement direct) : c'est une lecture pure, pas une mutation,
// donc GET est plus correct pour une API que le formulaire/POST du contrôleur web.
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'gestionnaire');

  const data = await buildStockReportData(authUser);
  const dateGeneration = new Date().toLocaleString('fr-FR');
  const buffer = await generatePdf(buildStockReportDoc(data, authUser, dateGeneration));

  const filename = `rapport_stock_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')}.pdf`;
  return pdfResponse(buffer, filename);
});
