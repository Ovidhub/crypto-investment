import {
  getAdminStats,
  getRecentTransactions,
  getRecentUsers,
} from "@/lib/admin";
import { formatUsd } from "@/lib/format";
import { isDemoMode } from "@/db";
import Link from "next/link";

export const dynamic = "force-dynamic";

export default async function AdminOverview() {
  const [stats, txns, users] = await Promise.all([
    getAdminStats(),
    getRecentTransactions(8),
    getRecentUsers(6),
  ]);

  const cards = [
    { label: "Total users", value: stats.users.toLocaleString(), accent: true },
    { label: "Waitlist signups", value: stats.waitlist.toLocaleString() },
    { label: "Transactions", value: stats.transactions.toLocaleString() },
    { label: "Trade volume", value: formatUsd(stats.volume, { compact: true }) },
    { label: "Custodied cash", value: formatUsd(stats.cash, { compact: true }) },
  ];

  return (
    <div className="space-y-8">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">
          Admin overview
        </h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          Platform-wide metrics and recent activity.
        </p>
      </div>

      {isDemoMode && (
        <div className="rounded-2xl border border-brand/40 bg-brand/10 px-5 py-4 text-sm text-[#d8c8ff]">
          <strong className="font-semibold text-white">Demo mode.</strong> This
          deployment uses in-memory sample data, so new accounts and changes
          are not permanently stored. Add a <code>DATABASE_URL</code> project
          variable to switch to a real Postgres database.
        </div>
      )}

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        {cards.map((c) => (
          <div
            key={c.label}
            className={`rounded-2xl border border-line p-5 ${
              c.accent
                ? "bg-gradient-to-br from-brand/15 to-brand-2/5"
                : "bg-card"
            }`}
          >
            <p className="text-xs text-[#9aa3b2]">{c.label}</p>
            <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
              {c.value}
            </p>
          </div>
        ))}
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Newest users</h2>
            <Link
              href="/admin/users"
              className="text-xs font-semibold text-brand hover:underline"
            >
              View all
            </Link>
          </div>
          <div className="divide-y divide-line/60">
            {users.map((u) => (
              <div
                key={u.id}
                className="flex items-center justify-between px-6 py-3.5 text-sm"
              >
                <div className="min-w-0">
                  <p className="truncate font-semibold text-white">{u.name}</p>
                  <p className="truncate text-xs text-[#7c8698]">{u.email}</p>
                </div>
                <span className="tabular-nums text-[#9aa3b2]">
                  {formatUsd(Number(u.balanceUsd))}
                </span>
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-2xl border border-line bg-card">
          <div className="border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Recent transactions</h2>
          </div>
          <div className="divide-y divide-line/60">
            {txns.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">No transactions yet.</p>
            ) : (
              txns.map((t) => (
                <div
                  key={t.id}
                  className="flex items-center justify-between px-6 py-3.5 text-sm"
                >
                  <div className="min-w-0">
                    <span
                      className={`font-semibold capitalize ${
                        t.type === "buy" || t.type === "deposit"
                          ? "text-accent"
                          : "text-[#f6465d]"
                      }`}
                    >
                      {t.type}
                    </span>
                    {t.symbol && (
                      <span className="ml-2 text-[#c7cedb]">{t.symbol}</span>
                    )}
                    <p className="truncate text-xs text-[#7c8698]">{t.email}</p>
                  </div>
                  <span className="tabular-nums text-[#9aa3b2]">
                    {formatUsd(Number(t.usdValue))}
                  </span>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
