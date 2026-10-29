import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { getCurrentUser } from "@/lib/auth";
import type { ReactNode } from "react";

export default async function LegalLayout({
  title,
  updated,
  children,
}: {
  title: string;
  updated: string;
  children: ReactNode;
}) {
  const me = await getCurrentUser();
  return (
    <>
      <Navbar loggedIn={!!me} />
      <main className="mx-auto max-w-3xl px-5 pb-24 pt-32 sm:px-8">
        <h1 className="font-display text-[clamp(2rem,4vw,2.8rem)] font-bold">{title}</h1>
        <p className="mt-2 text-xs text-[#7c8698]">Last updated {updated}</p>
        <div className="mt-8 space-y-6 text-sm leading-relaxed text-[#c7cedb]">
          {children}
        </div>
      </main>
      <Footer />
    </>
  );
}
