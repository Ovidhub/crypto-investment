import Link from "next/link";
import { requireAdmin } from "@/lib/auth";
import { fetchLiveCoins } from "@/lib/coins";
import { formatUsd } from "@/lib/format";
import {
  listDeskTrades,
  listPolicyAccounts,
  listSettledTrades,
  POLICY_LABELS,
  settleDueTrades,
  simDeskStats,
  SIM_PAYOUT_BP,
} from "@/lib/sim-trades";
import TradeSettlementPanel from "@/components/TradeSettlementPanel";
import SectionHeader from "@/components/SectionHeader";
import StatCard from "@/components/StatCard";
import StatusPill from "@/components/StatusPill";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

/** Module-scope so the render body stays pure for the lint rules. */
function isPastExpiry(iso: string): boolean {
  return Date.now() >= new Date(iso).getTime();
}

export default async function AdminTradesPage() {
  await requireAdmin();

  // Expired trades on accounts with a standing policy settle as soon as the
  // desk is opened, without an operator having to click each one.
  const auto = await settleDueTrades();

  const [trades, settled, stats, coins, policyAccounts] = await Promise.all([
    listDeskTrades(),
    listSettledTrades(25),
    simDeskStats(),
    fetchLiveCoins(),
    listPolicyAccounts(),
  ]);

  const prices: Record<string, number> = {};
  for (const coin of coins) prices[coin.id] = coin.price;

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Settlement desk"
        description="Every trade a customer opens lands here unless their account has a standing outcome policy. Deciding the outcome posts a balanced journal: a win pays the profit from the house payout account, a loss moves the stake to house revenue, a void returns the stake."
      />

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard
          label="Open trades"
          value={String(stats.openCount)}
          hint={`${formatUsd(stats.escrow)} held in escrow`}
          tone="brand"
        />
        <StatCard
          label="Paid out on wins"
          value={formatUsd(stats.paidOut)}
          hint={`${(SIM_PAYOUT_BP / 100).toFixed(0)}% of stake per win`}
        />
        <StatCard label="Stakes forfeited" value={formatUsd(stats.takenIn)} />
        <StatCard
          label="Desk net"
          value={formatUsd(stats.houseNet)}
          tone={stats.houseNet >= 0 ? "accent" : "warn"}
          hint="forfeits − payouts, on the books"
        />
      </div>

      <div className="rounded-2xl border border-brand/25 bg-brand/5 px-6 py-4 text-xs leading-relaxed text-[#c7cedb]">
        <span className="font-semibold text-white">How outcomes are recorded.</span>{" "}
        Three sources, all labelled on the customer&apos;s receipt: your decision
        (“operator decision”, with your account id and note), the live price
        (“settled at market”), or an account&apos;s standing policy
        (“house outcome policy”, which settles each trade when its window closes).
        The money always moves through a balanced ledger journal, never by editing a
        balance directly.
      </div>

      {auto.settled > 0 && (
        <div className="rounded-2xl border border-accent/30 bg-accent/5 px-6 py-4 text-xs text-[#c7cedb]">
          <span className="font-semibold text-accent">
            {auto.settled} trade{auto.settled === 1 ? "" : "s"} settled automatically
          </span>{" "}
          under account outcome policies just now
          {auto.failures > 0 ? `, ${auto.failures} failed` : ""}.
        </div>
      )}

      <div className="rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Accounts with a standing policy</h2>
          <p className="text-xs text-[#7c8698]">
            These accounts skip the manual queue once a trade reaches expiry. Change
            the policy on the customer&apos;s page.
          </p>
        </div>
        {policyAccounts.length === 0 ? (
          <p className="px-6 py-6 text-sm text-[#9aa3b2]">
            Every account is on <span className="text-[#c7cedb]">manual</span> — all
            trades wait for a decision here.
          </p>
        ) : (
          <div className="divide-y divide-line/60">
            {policyAccounts.map((row) => (
              <div key={row.userId} className="flex items-center justify-between gap-3 px-6 py-3.5 text-sm">
                <Link href={`/admin/users/${row.userId}`} className="min-w-0 hover:text-brand">
                  <span className="font-semibold text-white">{row.name}</span>
                  <span className="ml-2 text-[11px] text-[#7c8698]">{row.email}</span>
                </Link>
                <span
                  className={`shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${
                    row.policy === "win"
                      ? "bg-accent/15 text-accent"
                      : row.policy === "loss"
                        ? "bg-[#f6465d]/15 text-[#f6465d]"
                        : "bg-brand/15 text-brand"
                  }`}
                >
                  {POLICY_LABELS[row.policy]}
                </span>
              </div>
            ))}
          </div>
        )}
      </div>

      <TradeSettlementPanel
        trades={trades.map((t) => ({
          id: t.id,
          userId: t.userId,
          userName: t.userName,
          userEmail: t.userEmail,
          coinId: t.coinId,
          symbol: t.symbol,
          direction: t.direction,
          stakeUsd: t.stakeUsd,
          entryPrice: t.entryPrice,
          payoutBp: t.payoutBp,
          durationMinutes: t.durationMinutes,
          expiresAt: t.expiresAt,
          due: isPastExpiry(t.expiresAt),
        }))}
        prices={prices}
      />

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Recently settled</h2>
          <p className="text-xs text-[#7c8698]">
            Actors and before/after balances are in the{" "}
            <Link href="/admin/audit" className="text-brand hover:underline">
              audit trail
            </Link>
            .
          </p>
        </div>
        {settled.length === 0 ? (
          <EmptyState
            title="Nothing settled yet"
            body="Decided trades appear here with the outcome, decision source and note."
          />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[820px] text-sm">
              <thead>
                <tr className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                  <th className="px-6 py-3 font-semibold">Trade</th>
                  <th className="px-6 py-3 font-semibold">Customer</th>
                  <th className="px-6 py-3 font-semibold">Stake</th>
                  <th className="px-6 py-3 font-semibold">Outcome</th>
                  <th className="px-6 py-3 font-semibold">Payout</th>
                  <th className="px-6 py-3 font-semibold">Source</th>
                  <th className="px-6 py-3 font-semibold">Note</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {settled.map((t) => (
                  <tr key={t.id} className="align-top">
                    <td className="px-6 py-3">
                      <span className="font-semibold">{t.symbol}</span>{" "}
                      <span
                        className={`text-xs ${
                          t.direction === "long" ? "text-accent" : "text-[#f6465d]"
                        }`}
                      >
                        {t.direction}
                      </span>
                      <p className="text-[11px] text-[#7c8698]">
                        #{t.id} ·{" "}
                        {new Date(t.settledAt ?? t.openedAt).toLocaleDateString()}
                      </p>
                    </td>
                    <td className="px-6 py-3 text-xs">
                      {t.userName ? (
                        <Link
                          href={`/admin/users/${t.userId}`}
                          className="text-[#c7cedb] hover:text-brand"
                        >
                          {t.userName}
                        </Link>
                      ) : (
                        `#${t.userId}`
                      )}
                    </td>
                    <td className="px-6 py-3 tabular-nums">{formatUsd(Number(t.stakeUsd))}</td>
                    <td className="px-6 py-3">
                      <StatusPill status={t.status} />
                    </td>
                    <td className="px-6 py-3 tabular-nums">
                      {formatUsd(Number(t.payoutUsd ?? 0))}
                    </td>
                    <td className="px-6 py-3 text-xs capitalize text-[#9aa3b2]">
                      {t.outcomeSource ?? "—"}
                    </td>
                    <td className="max-w-[300px] px-6 py-3 text-xs text-[#9aa3b2]">
                      {t.settleNote ?? "—"}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
