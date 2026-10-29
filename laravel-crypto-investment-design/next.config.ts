import path from "node:path";
import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // The repository root has its own package-lock.json; pin the app root so
  // Turbopack does not guess the parent directory as the workspace root.
  turbopack: { root: path.resolve(process.cwd()) },
  // pg-mem / pg are Node-only packages; let the server require them at runtime.
  serverExternalPackages: ["pg", "pg-mem"],
  async headers() {
    // Baseline hardening. The CSP allows inline styles/scripts because Next.js
    // injects its own bootstrap markup; everything else stays same-origin.
    const csp = [
      "default-src 'self'",
      "script-src 'self' 'unsafe-inline'",
      "style-src 'self' 'unsafe-inline'",
      "img-src 'self' data: blob: https:",
      "font-src 'self' data:",
      "connect-src 'self'",
      "frame-ancestors 'none'",
      "base-uri 'self'",
      "form-action 'self'",
      "object-src 'none'",
    ].join("; ");

    return [
      {
        source: "/:path*",
        headers: [
          { key: "Content-Security-Policy", value: csp },
          { key: "X-Content-Type-Options", value: "nosniff" },
          { key: "Referrer-Policy", value: "strict-origin-when-cross-origin" },
          { key: "X-Frame-Options", value: "DENY" },
          { key: "Permissions-Policy", value: "camera=(), microphone=(), geolocation=()" },
          {
            key: "Strict-Transport-Security",
            value: "max-age=63072000; includeSubDomains; preload",
          },
        ],
      },
    ];
  },
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
