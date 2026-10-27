import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { getPortfolio } from "@/lib/portfolio";
import { userBalances } from "@/lib/ledger";
import { centsToNumber } from "@/lib/money";
import { formatPct, formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import CoinBadge from "@/components/CoinBadge";
import EmptyState from "@/components/EmptyState";
import StatCard from "@/components/StatCard";

export const dynamic = "force-dynamic";

export default async function PortfolioPage() {
  const me = await requireUser();
  const [portfolio, balances] = await Promise.all([
    getPortfolio(me.id),
    userBalances(me.id),
  ]);

  const total = portfolio.holdingsValue + centsToNumber(balances.cash) + centsToNumber(balances.locked);
  const unrealised = portfolio.positions.reduce((sum, p) => sum + p.unrealised, 0);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Holdings"
        description="Live prices valued against your simulated positions and recorded cost basis."
        action={
          <Link
            href="/dashboard/markets"
            className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
          >
            Trade
          </Link>
        }
      />

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="Total value" value={formatUsd(total)} tone="brand" />
        <StatCard label="Crypto at market" value={formatUsd(portfolio.holdingsValue)} />
        <StatCard label="Cash" value={formatUsd(centsToNumber(balances.cash))} />
        <StatCard
          label="Unrealised P/L"
          value={`${unrealised >= 0 ? "+" : ""}${formatUsd(unrealised)}`}
          tone={unrealised >= 0 ? "accent" : "warn"}
          hint="market value − recorded cost"
        />
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {portfolio.positions.length === 0 ? (
          <EmptyState
            title="No holdings"
            body="Buy your first coin with simulated cash to build a portfolio."
            action="Open the trading desk"
            href="/dashboard/markets"
          />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[760px] text-sm">
              <thead className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                <tr>
                  <th className="px-6 py-3 font-semibold">Asset</th>
                  <th className="px-6 py-3 font-semibold">Amount</th>
                  <th className="px-6 py-3 font-semibold">Price</th>
                  <th className="px-6 py-3 font-semibold">Value</th>
                  <th className="px-6 py-3 font-semibold">Cost basis</th>
                  <th className="px-6 py-3 font-semibold">P/L</th>
                  <th className="px-6 py-3 font-semibold">24h</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {portfolio.positions.map((p) => (
                  <tr key={p.coinId}>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <CoinBadge symbol={p.symbol} color={p.color} />
                        <div>
                          <p className="font-semibold text-white">{p.name}</p>
                          <p className="text-[11px] text-[#7c8698]">{p.symbol}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 tabular-nums text-[#c7cedb]">
                      {p.amount.toFixed(6)}
                    </td>
                    <td className="px-6 py-4 tabular-nums text-[#c7cedb]">
                      {formatUsd(p.price)}
                    </td>
                    <td className="px-6 py-4 tabular-nums font-semibold text-white">
                      {formatUsd(p.value)}
                    </td>
                    <td className="px-6 py-4 tabular-nums text-[#9aa3b2]">
                      {formatUsd(p.costBasis)}
                    </td>
                    <td
                      className={`px-6 py-4 tabular-nums ${
                        p.unrealised >= 0 ? "text-accent" : "text-[#f6465d]"
                      }`}
                    >
                      {p.unrealised >= 0 ? "+" : ""}
                      {formatUsd(p.unrealised)}
                    </td>
                    <td
                      className={`px-6 py-4 tabular-nums ${
                        p.change24h >= 0 ? "text-accent" : "text-[#f6465d]"
                      }`}
                    >
                      {formatPct(p.change24h)}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <p className="text-xs text-[#7c8698]">
        Cost basis is allocated pro rata from the ledger&apos;s crypto-at-cost
        balance, so the P/L column is an estimate rather than a per-lot record.
      </p>
    </div>
  );
}
