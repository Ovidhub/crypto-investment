import path from "node:path";
import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // The repository root has its own package-lock.json; pin the app root so
  // Turbopack does not guess the parent directory as the workspace root.
  turbopack: { root: path.resolve(process.cwd()) },
  // pg-mem / pg are Node-only packages; let the server require them at runtime.
  serverExternalPackages: ["pg", "pg-mem"],
  async redirects() {
    return [
      {
        source: "/admin/login",
        destination: "/login",
        permanent: false,
      },
    ];
  },
};

export default nextConfig;
