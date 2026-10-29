"use client";

import Link from "next/link";
import { useEffect, useState } from "react";

const LINKS = [
  { href: "/#features", label: "Features" },
  { href: "/#markets", label: "Markets" },
  { href: "/pricing", label: "Pricing" },
  { href: "/about", label: "About" },
  { href: "/contact", label: "Contact" },
];

export default function Navbar({
  loggedIn = false,
  siteName = "Enethi",
  logo = "",
}: {
  loggedIn?: boolean;
  siteName?: string;
  logo?: string;
}) {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 16);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header
      className={`fixed inset-x-0 top-0 z-50 transition-all duration-300 ${
        scrolled
          ? "border-b border-line/70 bg-ink/80 backdrop-blur-xl"
          : "border-b border-transparent bg-transparent"
      }`}
    >
      <nav className="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
        <Link href="/" className="flex items-center gap-2.5">
          {logo ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={logo} alt={siteName} className="h-9 w-9 rounded-xl object-cover" />
          ) : (
            <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand to-brand-2 font-display text-lg font-bold text-ink">
              {siteName.slice(0, 1).toUpperCase()}
            </span>
          )}
          <span className="font-display text-lg font-bold tracking-tight">
            {siteName}
          </span>
        </Link>

        <div className="hidden items-center gap-8 md:flex">
          {LINKS.map((l) => (
            <a
              key={l.href}
              href={l.href}
              className="text-sm text-[#9aa3b2] transition-colors hover:text-white"
            >
              {l.label}
            </a>
          ))}
        </div>

        <div className="hidden items-center gap-3 md:flex">
          {loggedIn ? (
            <a
              href="/dashboard"
              className="rounded-full bg-gradient-to-r from-brand to-brand-2 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-brand-2/30 transition-transform hover:scale-[1.03]"
            >
              Go to Dashboard
            </a>
          ) : (
            <>
              <a
                href="/login"
                className="rounded-full px-4 py-2 text-sm font-medium text-[#c7cedb] transition-colors hover:text-white"
              >
                Sign In
              </a>
              <a
                href="/register"
                className="rounded-full bg-gradient-to-r from-brand to-brand-2 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-brand-2/30 transition-transform hover:scale-[1.03]"
              >
                Start Investing
              </a>
            </>
          )}
        </div>

        <button
          onClick={() => setOpen((v) => !v)}
          className="grid h-10 w-10 place-items-center rounded-lg border border-line text-white md:hidden"
          aria-label="Toggle menu"
        >
          <div className="space-y-1.5">
            <span
              className={`block h-0.5 w-5 bg-white transition-transform ${open ? "translate-y-2 rotate-45" : ""}`}
            />
            <span
              className={`block h-0.5 w-5 bg-white transition-opacity ${open ? "opacity-0" : ""}`}
            />
            <span
              className={`block h-0.5 w-5 bg-white transition-transform ${open ? "-translate-y-2 -rotate-45" : ""}`}
            />
          </div>
        </button>
      </nav>

      {open && (
        <div className="border-t border-line bg-ink/95 px-5 py-4 backdrop-blur-xl md:hidden">
          <div className="flex flex-col gap-1">
            {LINKS.map((l) => (
              <a
                key={l.href}
                href={l.href}
                onClick={() => setOpen(false)}
                className="rounded-lg px-3 py-2.5 text-sm text-[#c7cedb] hover:bg-white/5 hover:text-white"
              >
                {l.label}
              </a>
            ))}
            <a
              href={loggedIn ? "/dashboard" : "/register"}
              onClick={() => setOpen(false)}
              className="mt-2 rounded-full bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-center text-sm font-semibold text-white"
            >
              {loggedIn ? "Go to Dashboard" : "Start Investing"}
            </a>
            {!loggedIn && (
              <a
                href="/login"
                onClick={() => setOpen(false)}
                className="rounded-full border border-line px-5 py-2.5 text-center text-sm font-semibold text-white"
              >
                Sign In
              </a>
            )}
          </div>
        </div>
      )}
    </header>
  );
}
