import type { Metadata } from "next";
import type { ReactNode } from "react";
import { Inter, Space_Grotesk } from "next/font/google";
import "./globals.css";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap",
});

const spaceGrotesk = Space_Grotesk({
  subsets: ["latin"],
  variable: "--font-space-grotesk",
  display: "swap",
});

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
    <html lang="en" className={`${inter.variable} ${spaceGrotesk.variable}`}>
      <body className="bg-ink text-[#e7ebf2] antialiased">{children}</body>
    </html>
  );
}
