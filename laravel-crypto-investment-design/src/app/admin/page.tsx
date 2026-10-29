import Link from "next/link";
import {
  getAdminStats,
  getRecentTransactions,
  getRecentUsers,
} from "@/lib/admin";
import { trialBalance, recentJournals } from "@/lib/ledger";
import { planStats } from "@/lib/plans";
import { formatUsd } from "@/lib/format";
import { isDemoMode } from "@/db";
import SectionHeader from "@/components/SectionHeader";
import StatCard from "@/components/StatCard";
import StatusPill from "@/components/StatusPill";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminOverview() {
  const [stats, txns, users, books, plans, journals] = await Promise.all([
    getAdminStats(),
    getRecentTransactions(8),
    getRecentUsers(6),
    trialBalance(),
    planStats(),
    recentJournals(5),
  ]);

  const queueTotal =
    stats.pendingDeposits + stats.pendingWithdrawals + stats.pendingKyc;

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Operations overview"
        description="Platform health, review queues and the state of the books."
      />

      {isDemoMode && (
        <Notice tone="warn" title="Ephemeral database">
          Running on the in-process database. Data resets on cold start and
          is not shared between instances. Set <code>DATABASE_URL</code> for
          persistence.
        </Notice>
      )}

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="Users" value={stats.users.toLocaleString()} tone="brand" />
        <StatCard
          label="Open review items"
          value={queueTotal}
          tone={queueTotal > 0 ? "warn" : "default"}
          hint={
            <Link href="/admin/requests" className="text-brand hover:underline">
              {stats.pendingDeposits} deposits · {stats.pendingWithdrawals} withdrawals
            </Link>
          }
        />
        <StatCard
          label="Active plans"
          value={stats.activeInvestments}
          hint={`${formatUsd(plans.principal)} principal locked`}
        />
        <StatCard
          label="Trailing trade volume"
          value={formatUsd(stats.volume, { compact: true })}
          hint="settled to plan members"
        />
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.15fr_1fr]">
        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <div>
              <h2 className="font-display font-bold">Books</h2>
              <p className="text-xs text-[#7c8698]">
                {books.balanced ? "Debits equal credits" : "Out of balance"}
              </p>
            </div>
            <Link href="/admin/books" className="text-xs font-semibold text-brand hover:underline">
              Open ledger
            </Link>
          </div>
          <dl className="divide-y divide-line/60">
            <div className="flex items-center justify-between px-6 py-3.5 text-sm">
              <dt className="text-[#9aa3b2]">Total debits</dt>
              <dd className="tabular-nums text-white">{formatUsd(Number(books.debits))}</dd>
            </div>
            <div className="flex items-center justify-between px-6 py-3.5 text-sm">
              <dt className="text-[#9aa3b2]">Total credits</dt>
              <dd className="tabular-nums text-white">{formatUsd(Number(books.credits))}</dd>
            </div>
            <div className="flex items-center justify-between px-6 py-3.5 text-sm">
              <dt className="text-[#9aa3b2]">Difference</dt>
              <dd
                className={`tabular-nums ${
                  books.balanced ? "text-accent" : "text-[#f6465d]"
                }`}
              >
                {formatUsd(Number(books.difference))}
              </dd>
            </div>
          </dl>
          <div className="border-t border-line px-6 py-4">
            <p className="text-[11px] font-semibold uppercase tracking-wider text-[#7c8698]">
              Latest journals
            </p>
            <div className="mt-2 space-y-2">
              {journals.length === 0 ? (
                <p className="text-xs text-[#7c8698]">No journals yet.</p>
              ) : (
                journals.map((j) => (
                  <div key={j.id} className="flex items-center justify-between text-xs">
                    <span className="truncate text-[#c7cedb]">{j.memo}</span>
                    <span className="ml-3 shrink-0 font-mono text-[10px] text-[#5d6675]">
                      {j.kind}
                    </span>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>

        <div className="space-y-6">
          <div className="rounded-2xl border border-line bg-card">
            <div className="flex items-center justify-between border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Newest users</h2>
              <Link href="/admin/users" className="text-xs font-semibold text-brand hover:underline">
                All users
              </Link>
            </div>
            <div className="divide-y divide-line/60">
              {users.map((u) => (
                <div key={u.id} className="flex items-center justify-between px-6 py-3.5 text-sm">
                  <div className="min-w-0">
                    <p className="truncate font-semibold text-white">{u.name}</p>
                    <p className="truncate text-xs text-[#7c8698]">{u.email}</p>
                  </div>
                  <StatusPill status={u.kycStatus} />
                </div>
              ))}
            </div>
          </div>

          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Recent activity</h2>
            </div>
            <div className="divide-y divide-line/60">
              {txns.length === 0 ? (
                <p className="p-6 text-sm text-[#9aa3b2]">No transactions yet.</p>
              ) : (
                txns.map((t) => (
                  <div key={t.id} className="flex items-center justify-between px-6 py-3.5 text-sm">
                    <div className="min-w-0">
                      <span className="font-semibold capitalize text-white">{t.type}</span>
                      {t.symbol && <span className="ml-2 text-[#c7cedb]">{t.symbol}</span>}
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
    </div>
  );
}
