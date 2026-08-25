import path from 'node:path';

// pdfmake 0.3 exporte un singleton MUTABLE avec des exports nommés (setFonts,
// createPdf, ...) qui s'auto-modifie (this.fonts = ...), pas une classe
// PdfPrinter comme dans les versions <0.2. `import * as pdfMake from 'pdfmake'`
// echoue donc a l'execution ("object is not extensible") : Turbopack/Next.js
// expose un objet namespace ESM fige (scelle par la spec) pour ce type d'import,
// alors que pdfmake a besoin d'y ajouter des proprietes apres coup. require()
// direct contourne ce figeage en donnant la vraie reference mutable de l'objet.
// eslint-disable-next-line @typescript-eslint/no-require-imports
const pdfMake = require('pdfmake') as typeof import('pdfmake');

// On derive le type du document depuis createPdf() plutot que d'importer
// 'pdfmake/interfaces' directement (le sous-chemin n'est pas garanti resoluble
// selon la config TS du projet).
export type DocDefinition = Parameters<typeof pdfMake.createPdf>[0];
export type { Content, Style } from 'pdfmake';

const fontsDir = path.join(process.cwd(), 'node_modules/pdfmake/fonts/Roboto');

let configured = false;
function ensureConfigured() {
  if (configured) return;
  // Le paquet npm de pdfmake ne fournit que Regular/Medium/Italic/MediumItalic
  // (pas de vraie graisse "Bold") : Medium fait office de gras, comme dans les
  // exemples officiels de pdfmake.
  pdfMake.setFonts({
    Roboto: {
      normal: path.join(fontsDir, 'Roboto-Regular.ttf'),
      bold: path.join(fontsDir, 'Roboto-Medium.ttf'),
      italics: path.join(fontsDir, 'Roboto-Italic.ttf'),
      bolditalics: path.join(fontsDir, 'Roboto-MediumItalic.ttf'),
    },
  });
  pdfMake.setLocalAccessPolicy(() => true);
  pdfMake.setUrlAccessPolicy(() => false);
  configured = true;
}

// pdfkit/fontkit forme les ligatures OpenType de Roboto (fi, fl, ...) mais perd
// visuellement le second caractere dans le PDF genere ("Benefice" -> "Benefce") --
// bug connu de rendu de ligatures avec ce pipeline. Insérer un caractère
// invisible (ZWNJ, ZWSP...) entre les deux lettres pour bloquer la ligature ne
// marche pas ici : Roboto n'a pas de glyphe pour ces caractères de contrôle et
// pdfkit dessine alors un carré "glyphe manquant" à la place -- pire que le bug
// d'origine. La vraie parade est de couper la chaîne en deux fragments de texte
// distincts juste après le "f" : la ligature ne se forme qu'entre lettres d'un
// même run de texte, donc deux runs consécutifs ne fusionnent jamais.
// On en profite aussi pour remplacer les espaces insecables (notamment le
// separateur de milliers de toLocaleString('fr-FR')) que la police Roboto
// embarquee ne dessine pas (glyphe manquant, affiche comme un carre).
const NBSP_PATTERN = new RegExp(`[\\u00A0\\u202F]`, 'g');
const LIGATURE_PATTERN = /f(?=[il])/g;

function sanitizeText(text: string): string | string[] {
  const withoutNbsp = text.replace(NBSP_PATTERN, ' ');
  const matches = [...withoutNbsp.matchAll(LIGATURE_PATTERN)];
  if (matches.length === 0) return withoutNbsp;

  const parts: string[] = [];
  let lastIndex = 0;
  for (const match of matches) {
    const splitAt = match.index + 1; // juste après le "f"
    parts.push(withoutNbsp.slice(lastIndex, splitAt));
    lastIndex = splitAt;
  }
  parts.push(withoutNbsp.slice(lastIndex));
  return parts;
}

// `insideTextProp` distingue deux contextes où pdfmake interprète un tableau de
// chaînes différemment : sous une clé `text`, un tableau = des fragments d'un
// même paragraphe (inline). Ailleurs (ex: une cellule de tableau qui est une
// simple chaîne), un tableau = plusieurs blocs empilés verticalement. Un
// fragment coupé doit donc être ré-enveloppé dans `{ text: [...] }` dans ce
// second cas pour rester un seul et même contenu, au lieu de "sauter une ligne".
function sanitizeDeep<T>(value: T, insideTextProp = false): T {
  if (typeof value === 'string') {
    const sanitized = sanitizeText(value);
    if (Array.isArray(sanitized)) {
      return (insideTextProp ? sanitized : { text: sanitized }) as unknown as T;
    }
    return sanitized as unknown as T;
  }
  if (Array.isArray(value)) return value.map((v) => sanitizeDeep(v, insideTextProp)) as unknown as T;
  if (value && typeof value === 'object') {
    const out: Record<string, unknown> = {};
    for (const [k, v] of Object.entries(value)) out[k] = sanitizeDeep(v, k === 'text');
    return out as T;
  }
  return value;
}

export async function generatePdf(docDefinition: DocDefinition): Promise<Buffer> {
  ensureConfigured();
  const pdf = pdfMake.createPdf(
    sanitizeDeep({
      defaultStyle: { font: 'Roboto', fontSize: 9 },
      pageMargins: [30, 30, 30, 30],
      ...docDefinition,
    }),
  );
  return pdf.getBuffer();
}

export function pdfResponse(buffer: Buffer, filename: string): Response {
  return new Response(new Uint8Array(buffer), {
    headers: {
      'Content-Type': 'application/pdf',
      'Content-Disposition': `attachment; filename="${filename}"`,
    },
  });
}
