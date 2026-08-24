import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Le repo est un monorepo avec un package-lock.json à la racine (app Laravel) :
  // sans ça, Next.js infère à tort la racine du repo comme workspace root.
  turbopack: {
    root: __dirname,
  },
};

export default nextConfig;
