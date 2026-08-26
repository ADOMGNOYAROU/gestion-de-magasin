import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Le repo est un monorepo avec un package-lock.json à la racine (app Laravel) :
  // sans ça, Next.js infère à tort la racine du repo comme workspace root.
  turbopack: {
    root: __dirname,
  },
  // `firebase-admin` est déjà auto-externalisé par Next.js, mais pas `jwks-rsa`
  // (utilisé par firebase-admin/auth) : son propre require('jose') plante en
  // prod sur Vercel (ERR_REQUIRE_ESM, jose est distribué en ESM pur). Les
  // marquer externes fait passer leur résolution par le require() natif de
  // Node plutôt que par le bundler, qui ne gère pas cette interop CJS/ESM.
  serverExternalPackages: ["jwks-rsa", "jose"],
};

export default nextConfig;
