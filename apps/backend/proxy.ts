import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

// `middleware.ts` a été renommé `proxy.ts` en Next.js 16 (même fonctionnement,
// juste un nom de fichier/export différent) — voir node_modules/next/dist/docs/
// .../file-conventions/proxy.md. Sert uniquement à autoriser Angular (une
// origine différente : port distinct en dev, domaine distinct en prod) à
// appeler cette API — les credentials Firebase restent vérifiés normalement
// par authenticate()/authenticateWithRole() dans chaque route.
const allowedOrigins = ['http://localhost:4200', process.env.FRONTEND_URL].filter(
  (origin): origin is string => Boolean(origin),
);

const corsHeaders = {
  'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization',
};

export function proxy(request: NextRequest) {
  const origin = request.headers.get('origin') ?? '';
  const isAllowedOrigin = allowedOrigins.includes(origin);
  const isPreflight = request.method === 'OPTIONS';

  if (isPreflight) {
    return NextResponse.json(
      {},
      {
        headers: {
          ...(isAllowedOrigin && { 'Access-Control-Allow-Origin': origin }),
          ...corsHeaders,
        },
      },
    );
  }

  const response = NextResponse.next();
  if (isAllowedOrigin) {
    response.headers.set('Access-Control-Allow-Origin', origin);
  }
  for (const [key, value] of Object.entries(corsHeaders)) {
    response.headers.set(key, value);
  }
  return response;
}

export const config = {
  matcher: '/api/:path*',
};
