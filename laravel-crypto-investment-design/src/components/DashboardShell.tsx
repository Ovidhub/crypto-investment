"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useState, type ReactNode } from "react";

type NavItem = { href: string; label: string; icon: string };

export default function DashboardShell({
  children,
  user,
  variant = "user",
}: {
  children: ReactNode;
  user: { name: string; email: string; role: string };
  variant?: "user" | "admin";
}) {
  const pathname = usePathname();
  const router = useRouter();
  const [open, setOpen] = useState(false);

  const userNav: NavItem[] = [
    { href: "/dashboard", label: "Overview", icon: "▚" },
    { href: "/dashboard/markets", label: "Trade", icon: "⇅" },
    { href: "/dashboard/wallet", label: "Wallet", icon: "▦" },
    { href: "/dashboard/transactions", label: "Transactions", icon: "≡" },
  ];
  const adminNav: NavItem[] = [
    { href: "/admin", label: "Overview", icon: "▚" },
    { href: "/admin/users", label: "Users", icon: "☰" },
    { href: "/admin/waitlist", label: "Waitlist", icon: "✦" },
  ];
  const nav = variant === "admin" ? adminNav : userNav;

  async function logout() {
    await fetch("/api/auth/logout", { method: "POST" });
    router.push("/login");
    router.refresh();
  }

  const initials = user.name
    .split(" ")
    .map((p) => p[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();

  return (
    <div className="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
      {/* Sidebar */}
      <aside
        className={`fixed inset-y-0 left-0 z-40 w-[260px] border-r border-line bg-panel transition-transform lg:static lg:translate-x-0 ${
          open ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        <div className="flex h-full flex-col">
          <div className="flex h-16 items-center gap-2.5 border-b border-line px-6">
            <Link href="/" className="flex items-center gap-2.5">
              <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand to-brand-2 font-display text-lg font-bold text-ink">
                E
              </span>
              <span className="font-display text-lg font-bold">Enethi</span>
            </Link>
            {variant === "admin" && (
              <span className="ml-auto rounded-full bg-brand/15 px-2 py-0.5 text-[10px] font-bold text-brand">
                ADMIN
              </span>
            )}
          </div>

          <nav className="flex-1 space-y-1 p-4">
            {nav.map((item) => {
              const active =
                pathname === item.href ||
                (item.href !== "/dashboard" &&
                  item.href !== "/admin" &&
                  pathname.startsWith(item.href));
              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => setOpen(false)}
                  className={`flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-colors ${
                    active
                      ? "bg-gradient-to-r from-brand/20 to-brand-2/10 text-white"
                      : "text-[#9aa3b2] hover:bg-white/5 hover:text-white"
                  }`}
                >
                  <span className="text-base">{item.icon}</span>
                  {item.label}
                </Link>
              );
            })}
          </nav>

          <div className="border-t border-line p-4">
            <div className="flex items-center gap-3 rounded-xl bg-card p-3">
              <span className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand to-brand-2 text-sm font-bold text-ink">
                {initials}
              </span>
              <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-semibold text-white">
                  {user.name}
                </p>
                <p className="truncate text-xs text-[#7c8698]">{user.email}</p>
              </div>
            </div>
            <button
              onClick={logout}
              className="mt-3 w-full rounded-xl border border-line px-4 py-2.5 text-sm font-medium text-[#c7cedb] transition-colors hover:border-[#f6465d] hover:text-[#f6465d]"
            >
              Sign out
            </button>
          </div>
        </div>
      </aside>

      {open && (
        <div
          onClick={() => setOpen(false)}
          className="fixed inset-0 z-30 bg-black/60 lg:hidden"
        />
      )}

      {/* Main */}
      <div className="flex min-w-0 flex-col">
        <header className="flex h-16 items-center justify-between border-b border-line bg-ink/80 px-5 backdrop-blur-xl lg:hidden">
          <button
            onClick={() => setOpen(true)}
            className="grid h-10 w-10 place-items-center rounded-lg border border-line"
            aria-label="Open menu"
          >
            ☰
          </button>
          <span className="font-display font-bold">
            {variant === "admin" ? "Admin" : "Dashboard"}
          </span>
          <span className="w-10" />
        </header>
        <main className="flex-1 p-5 sm:p-8">{children}</main>
      </div>
    </div>
  );
}
