import type { Metadata } from "next";
import type { ReactNode } from "react";
import "@fontsource/inter/latin-400.css";
import "@fontsource/inter/latin-500.css";
import "@fontsource/inter/latin-600.css";
import "@fontsource/inter/latin-700.css";
import "@fontsource/space-grotesk/latin-500.css";
import "@fontsource/space-grotesk/latin-600.css";
import "@fontsource/space-grotesk/latin-700.css";
import "./globals.css";

export const metadata: Metadata = {
  title: "Enethi — Invest In Crypto, Earn Passive Income Easily",
  description:
    "Enethi is a fast, secure crypto investment platform. Track 99+ coins, build a passive income portfolio, and trade with confidence.",
  keywords: [
    "crypto",
    "investment",
    "bitcoin",
    "ethereum",
    "trading",
    "passive income",
  ],
};

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body className="bg-ink text-[#e7ebf2] antialiased">{children}</body>
    </html>
  );
}
