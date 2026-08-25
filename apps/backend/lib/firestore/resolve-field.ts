import { getDb } from '../firebase-admin';

// Résout un champ d'un document lié (ex: le nom d'un magasin à partir de son id),
// pour reproduire les `->with(...)` / accesseurs de relation Eloquent dans les réponses JSON.
export async function resolveField(
  collection: string,
  id: string | null | undefined,
  field: string,
): Promise<string | null> {
  if (!id) return null;
  const doc = await getDb().collection(collection).doc(id).get();
  if (!doc.exists) return null;
  const value = (doc.data() as Record<string, unknown>)[field];
  return typeof value === 'string' ? value : null;
}
