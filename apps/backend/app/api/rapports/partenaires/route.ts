import { withErrorHandling } from '@/lib/api-handler';
import { authenticateWithRole } from '@/lib/auth';
import { buildPartenairesReportData } from '@/lib/rapports/partenaires-report';
import { buildPartenairesReportDoc } from '@/lib/rapports/partenaires-report-pdf';
import { generatePdf, pdfResponse } from '@/lib/rapports/pdf';

// Reprend RapportController::rapportPartenairesPDF() (Gate::authorize('view-rapports-partenaires'),
// admin ou gestionnaire — cf. AuthServiceProvider.php).
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'gestionnaire');

  const data = await buildPartenairesReportData(authUser);
  const dateGeneration = new Date().toLocaleString('fr-FR');
  const buffer = await generatePdf(buildPartenairesReportDoc(data, authUser, dateGeneration));

  const filename = `rapport_partenaires_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')}.pdf`;
  return pdfResponse(buffer, filename);
});
