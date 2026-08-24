import { withErrorHandling } from '@/lib/api-handler';
import { ApiError, authenticateWithRole } from '@/lib/auth';
import { generatePdf, pdfResponse } from '@/lib/rapports/pdf';
import { buildVentesReportData } from '@/lib/rapports/ventes-report';
import { buildVentesReportExcel } from '@/lib/rapports/ventes-report-excel';
import { buildVentesReportDoc } from '@/lib/rapports/ventes-report-pdf';

// Reprend RapportController::rapportVentesPDF()/rapportVentesExcel(). GET avec
// un paramètre `format` plutôt que deux routes web séparées (PDF vs Excel) —
// c'est une lecture pure côté API, donc GET est plus correct que le
// formulaire/POST du contrôleur web d'origine.
export const GET = withErrorHandling(async (request) => {
  const authUser = await authenticateWithRole(request, 'gestionnaire');

  const { searchParams } = new URL(request.url);
  const dateDebut = searchParams.get('date_debut');
  const dateFin = searchParams.get('date_fin');
  const format = searchParams.get('format') === 'excel' ? 'excel' : 'pdf';

  if (!dateDebut || !dateFin) {
    throw new ApiError(422, 'date_debut et date_fin sont requis.');
  }
  if (dateFin < dateDebut) {
    throw new ApiError(422, 'date_fin doit être postérieure ou égale à date_debut.');
  }

  const filters = {
    dateDebut,
    dateFin,
    magasinId: searchParams.get('magasin_id'),
    boutiqueId: searchParams.get('boutique_id'),
  };

  const data = await buildVentesReportData(authUser, filters);
  const dateGeneration = new Date().toLocaleString('fr-FR');

  if (format === 'excel') {
    const buffer = await buildVentesReportExcel(data, filters, authUser, dateGeneration);
    return new Response(new Uint8Array(buffer), {
      headers: {
        'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition': `attachment; filename="rapport_ventes_${dateDebut}_au_${dateFin}.xlsx"`,
      },
    });
  }

  const buffer = await generatePdf(buildVentesReportDoc(data, filters, authUser, dateGeneration));
  return pdfResponse(buffer, `rapport_ventes_${dateDebut}_au_${dateFin}.pdf`);
});
