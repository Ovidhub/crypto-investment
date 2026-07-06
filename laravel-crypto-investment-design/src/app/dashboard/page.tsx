import { getCurrentUser } from "@/lib/auth";
import { getPortfolio, getTransactions } from "@/lib/portfolio";
import { formatUsd, formatPct } from "@/lib/format";
import CoinBadge from "@/components/CoinBadge";
import Sparkline from "@/components/Sparkline";
import PortfolioDonut from "@/components/PortfolioDonut";
import Link from "next/link";

export const dynamic = "force-dynamic";

export default async function DashboardOverview() {
  const me = (await getCurrentUser())!;
  const cash = Number(me.balanceUsd);
  const [portfolio, txns] = await Promise.all([
    getPortfolio(me.id),
    getTransactions(me.id, 6),
  ]);
  const totalValue = portfolio.holdingsValue + cash;

  return (
    <div className="space-y-8">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">
          Welcome back, {me.name.split(" ")[0]} 👋
        </h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          Here&apos;s how your portfolio is performing today.
        </p>
      </div>

      {/* Summary cards */}
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div className="rounded-2xl border border-line bg-gradient-to-br from-brand/15 to-brand-2/5 p-5">
          <p className="text-xs text-[#9aa3b2]">Total balance</p>
          <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
            {formatUsd(totalValue)}
          </p>
          <p
            className={`mt-1 text-xs font-semibold ${portfolio.totalChange24h >= 0 ? "text-accent" : "text-[#f6465d]"}`}
          >
            {formatPct(portfolio.totalChange24h)} 24h
          </p>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Holdings value</p>
          <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
            {formatUsd(portfolio.holdingsValue)}
          </p>
          <p className="mt-1 text-xs text-[#7c8698]">
            {portfolio.positions.length} assets
          </p>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Available cash</p>
          <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
            {formatUsd(cash)}
          </p>
          <Link
            href="/dashboard/wallet"
            className="mt-1 inline-block text-xs font-semibold text-brand hover:underline"
          >
            Deposit funds →
          </Link>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Quick action</p>
          <Link
            href="/dashboard/markets"
            className="mt-3 inline-block rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
          >
            Trade now
          </Link>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        {/* Holdings table */}
        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Your assets</h2>
            <Link
              href="/dashboard/markets"
              className="text-xs font-semibold text-brand hover:underline"
            >
              Manage
            </Link>
          </div>
          {portfolio.positions.length === 0 ? (
            <div className="p-10 text-center">
              <p className="text-sm text-[#9aa3b2]">
                You don&apos;t hold any assets yet.
              </p>
              <Link
                href="/dashboard/markets"
                className="mt-4 inline-block rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
              >
                Buy your first coin
              </Link>
            </div>
          ) : (
            <div className="divide-y divide-line/60">
              {portfolio.positions.map((p) => (
                <div
                  key={p.coinId}
                  className="flex items-center gap-4 px-6 py-4"
                >
                  <CoinBadge symbol={p.symbol} color={p.color} />
                  <div className="min-w-0 flex-1">
                    <p className="font-semibold text-white">{p.name}</p>
                    <p className="text-xs text-[#7c8698] tabular-nums">
                      {p.amount.toFixed(6)} {p.symbol}
                    </p>
                  </div>
                  <div className="hidden sm:block">
                    <Sparkline data={p.sparkline} up={p.change24h >= 0} width={90} height={32} />
                  </div>
                  <div className="text-right">
                    <p className="font-semibold tabular-nums text-white">
                      {formatUsd(p.value)}
                    </p>
                    <p
                      className={`text-xs font-semibold ${p.change24h >= 0 ? "text-accent" : "text-[#f6465d]"}`}
                    >
                      {formatPct(p.change24h)}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Allocation + recent */}
        <div className="space-y-6">
          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="mb-4 font-display font-bold">Allocation</h2>
            <PortfolioDonut positions={portfolio.positions} cash={cash} />
          </div>

          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Recent activity</h2>
            </div>
            {txns.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">No activity yet.</p>
            ) : (
              <div className="divide-y divide-line/60">
                {txns.map((t) => (
                  <div
                    key={t.id}
                    className="flex items-center justify-between px-6 py-3.5 text-sm"
                  >
                    <div>
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
                    </div>
                    <span className="tabular-nums text-[#9aa3b2]">
                      {formatUsd(Number(t.usdValue))}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
