import { ZodError } from 'zod';
import { ApiError } from './auth';

// Uniformise la gestion des erreurs pour tous les Route Handlers de l'API :
// ApiError -> son propre status, ZodError -> 422, tout le reste -> 500.
export function withErrorHandling(handler: (request: Request, context: any) => Promise<Response>) {
  return async (request: Request, context: any): Promise<Response> => {
    try {
      return await handler(request, context);
    } catch (error) {
      if (error instanceof ApiError) {
        return Response.json({ message: error.message }, { status: error.status });
      }
      if (error instanceof ZodError) {
        return Response.json({ message: 'Données invalides', errors: error.flatten() }, { status: 422 });
      }
      console.error(error);
      return Response.json({ message: 'Erreur interne du serveur' }, { status: 500 });
    }
  };
}
