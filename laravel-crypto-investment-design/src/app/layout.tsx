import type { Metadata } from "next";
import { getSiteContent } from "@/lib/content";
import type { ReactNode } from "react";
import "@fontsource/inter/latin-400.css";
import "@fontsource/inter/latin-500.css";
import "@fontsource/inter/latin-600.css";
import "@fontsource/inter/latin-700.css";
import "@fontsource/space-grotesk/latin-500.css";
import "@fontsource/space-grotesk/latin-600.css";
import "@fontsource/space-grotesk/latin-700.css";
import "./globals.css";

/**
 * Every page renders per request: copy, prices and session all come from the
 * database, so nothing may be baked in at build time (and the build must never
 * try to reach the database).
 */
export const dynamic = "force-dynamic";

/** Title and description come from the CMS so the brand stays consistent. */
export async function generateMetadata(): Promise<Metadata> {
  const content = await getSiteContent();
  const name = content["brand.name"] || "Actuatrades";
  const tagline = content["brand.tagline"] || "Crypto investing platform";
  return {
    title: `${name} — ${tagline}`,
    description: content["brand.description"],
    keywords: [
      "crypto",
      "investment",
      "bitcoin",
      "ethereum",
      "trading",
      "passive income",
    ],
  };
}

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body className="bg-ink text-[#e7ebf2] antialiased">{children}</body>
    </html>
  );
}
