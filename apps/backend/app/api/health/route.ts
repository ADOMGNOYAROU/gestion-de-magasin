export async function GET() {
  return Response.json({
    status: 'ok',
    service: 'gestion-backend',
    timestamp: new Date().toISOString(),
  });
}
