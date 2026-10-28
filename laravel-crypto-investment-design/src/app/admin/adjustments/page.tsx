import Link from "next/link";
import { requireAdmin } from "@/lib/auth";
import { usersWithMeta } from "@/lib/admin";
import { adjustmentTotals, listAdjustments } from "@/lib/adjustments";
import { formatUsd } from "@/lib/format";
import WalletAdjustmentForm from "@/components/WalletAdjustmentForm";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";
import StatCard from "@/components/StatCard";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

export default async function AdminAdjustmentsPage() {
  const me = await requireAdmin();
  const [accounts, history, totals] = await Promise.all([
    usersWithMeta(300),
    listAdjustments(40),
    adjustmentTotals(),
  ]);

  const options = accounts
    .filter((u) => u.id !== me.id && u.status === "active")
    .map((u) => ({
      id: u.id,
      name: u.name,
      email: u.email,
      cash: u.balanceUsd,
    }));

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Wallet adjustments"
        description="Credit or debit a simulated balance when a customer needs a correction. Every action is journaled, reasoned and audited."
      />

      <div className="grid gap-4 sm:grid-cols-3">
        <StatCard
          label="Credited by operators"
          value={formatUsd(totals.credited)}
          tone="accent"
          hint={`${totals.count} adjustment${totals.count === 1 ? "" : "s"} recorded`}
        />
        <StatCard label="Debited by operators" value={formatUsd(totals.debited)} />
        <StatCard
          label="Net effect"
          value={formatUsd(totals.credited - totals.debited)}
          hint="moves against operator equity, never revenue"
        />
      </div>

      <div className="grid gap-6 lg:grid-cols-[1fr_1.25fr]">
        <WalletAdjustmentForm users={options} />

        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <div>
              <h2 className="font-display font-bold">Recent adjustments</h2>
              <p className="text-xs text-[#7c8698]">
                Actors and before/after balances live in the{" "}
                <Link href="/admin/audit" className="text-brand hover:underline">
                  audit trail
                </Link>
                .
              </p>
            </div>
          </div>
          {history.length === 0 ? (
            <EmptyState
              title="No adjustments yet"
              body="Credits and debits you make will be listed here with their reason."
            />
          ) : (
            <div className="divide-y divide-line/60">
              {history.map((row) => (
                <div key={`${row.direction}-${row.id}`} className="px-6 py-4">
                  <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                      <Link
                        href={`/admin/users/${row.userId}`}
                        className="truncate text-sm font-semibold text-white hover:text-brand"
                      >
                        {row.userName ?? `User #${row.userId}`}
                      </Link>
                      <p className="truncate text-xs text-[#7c8698]">{row.userEmail}</p>
                    </div>
                    <span
                      className={`shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${
                        row.direction === "credit"
                          ? "bg-accent/15 text-accent"
                          : "bg-[#f6465d]/15 text-[#f6465d]"
                      }`}
                    >
                      {row.direction === "credit" ? "+" : "−"}
                      {formatUsd(Number(row.amountUsd))}
                    </span>
                  </div>
                  <p className="mt-1.5 text-xs text-[#c7cedb]">{row.reason ?? "No reason recorded"}</p>
                  <p className="text-[11px] text-[#5d6675]">
                    {new Date(row.createdAt).toLocaleString("en-US")}
                  </p>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <Notice tone="info" title="Why this is an adjustment, not a profit credit">
        An adjustment moves value between platform equity and the customer&apos;s
        wallet. It is stored as <code>adjustment_credit</code> /{" "}
        <code>adjustment_debit</code>, a different row type from{" "}
        <code>interest</code>, <code>buy</code> or <code>sell</code>. It cannot
        inflate reported trading performance, and the customer always sees the
        reason you wrote.
      </Notice>
    </div>
  );
}
