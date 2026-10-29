import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { getActivity, getPortfolio } from "@/lib/portfolio";
import { listNotifications } from "@/lib/notifications";
import { myInvestments } from "@/lib/plans";
import { userBalances } from "@/lib/ledger";
import { fetchLiveCoins } from "@/lib/coins";
import {
  SIM_DURATIONS,
  SIM_MAX_OPEN_TRADES,
  SIM_PAYOUT_BP,
  listUserTrades,
  settleDueTrades,
} from "@/lib/sim-trades";
import { centsToNumber, centsToUsd } from "@/lib/money";
import { formatPct, formatUsd } from "@/lib/format";
import CoinBadge from "@/components/CoinBadge";
import Sparkline from "@/components/Sparkline";
import PortfolioDonut from "@/components/PortfolioDonut";
import StatCard from "@/components/StatCard";
import StatusPill from "@/components/StatusPill";
import Notice from "@/components/Notice";
import EmptyState from "@/components/EmptyState";
import SimTradeTicket from "@/components/SimTradeTicket";
import MarketNews from "@/components/MarketNews";

export const dynamic = "force-dynamic";

export default async function DashboardOverview() {
  const me = await requireUser();

  // A trade whose window closed settles under the account's standing policy, so
  // the overview never shows a position that is already decided.
  await settleDueTrades({ userId: me.id });

  const [portfolio, activity, notes, investments, balances, coins, trades] =
    await Promise.all([
      getPortfolio(me.id),
      getActivity(me.id, 6),
      listNotifications(me.id, 3),
      myInvestments(me.id),
      userBalances(me.id),
      fetchLiveCoins(),
      listUserTrades(me.id, 20),
    ]);

  const cash = centsToNumber(balances.cash);
  const locked = centsToNumber(balances.locked);
  const activePlans = investments.filter((i) => i.status === "active");
  const earned = investments.reduce((sum, i) => sum + Number(i.earnedUsd), 0);
  const stake = centsToNumber(balances.stake);
  const totalValue = portfolio.holdingsValue + cash + locked + stake;
  const openTrades = trades.filter((t) => t.status === "open");
  const priceOf = new Map(coins.map((c) => [c.id, c.price]));

  return (
    <div className="space-y-8">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 className="font-display text-2xl font-bold sm:text-3xl">
            Welcome back, {me.name.split(" ")[0]}
          </h1>
          <p className="mt-1 text-sm text-[#9aa3b2]">
            Portfolio value across cash, holdings, locked plans and open trades.
          </p>
        </div>
        <Link
          href="/dashboard/markets"
          className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
        >
          Trade
        </Link>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard
          label="Portfolio value"
          value={formatUsd(totalValue)}
          tone="brand"
          hint={<span>{formatPct(portfolio.totalChange24h)} 24h on holdings</span>}
        />
        <StatCard
          label="Available cash"
          value={formatUsd(cash)}
          hint={
            <Link href="/dashboard/wallet" className="text-brand hover:underline">
              Add funds →
            </Link>
          }
        />
        <StatCard
          label="Locked in plans"
          value={formatUsd(locked)}
          hint={`${activePlans.length} active plan${activePlans.length === 1 ? "" : "s"}`}
        />
        <StatCard
          label="Yield earned"
          value={formatUsd(earned)}
          hint="Credited by the accrual job"
        />
      </div>

      {me.kycStatus !== "verified" && (
        <Notice tone="warn" title="Verification is optional to get started">
          <p>
            Verify your identity to lift limits and unlock withdrawals. It takes a
            couple of minutes and your details stay encrypted.{" "}
            <Link href="/dashboard/kyc" className="font-semibold text-brand hover:underline">
              Open verification →
            </Link>
          </p>
        </Notice>
      )}

      <div className="grid gap-6 lg:grid-cols-[1fr_1fr]">
        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-start justify-between gap-3 border-b border-line px-6 py-4">
            <div>
              <h2 className="font-display font-bold">Quick trade</h2>
              <p className="mt-0.5 text-xs text-[#9aa3b2]">
                Take a position on live prices straight from your overview.
              </p>
            </div>
            <Link
              href="/dashboard/trades"
              className="shrink-0 text-xs font-semibold text-brand hover:underline"
            >
              Trade desk →
            </Link>
          </div>
          <div className="p-6">
            <SimTradeTicket
              coins={coins.slice(0, 12).map((c) => ({
                id: c.id,
                symbol: c.symbol,
                name: c.name,
                price: c.price,
              }))}
              cash={cash}
              openCount={openTrades.length}
              maxOpen={SIM_MAX_OPEN_TRADES}
              payoutBp={SIM_PAYOUT_BP}
              durations={SIM_DURATIONS}
            />
          </div>
        </div>

        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Open positions</h2>
            <span className="text-[11px] uppercase tracking-wide text-[#7c8698]">
              {openTrades.length}/{SIM_MAX_OPEN_TRADES} open
            </span>
          </div>
          {openTrades.length === 0 ? (
            <EmptyState
              title="No open positions"
              body="Positions you open on the trade desk appear here until they settle."
              action="See settled trades"
              href="/dashboard/trades"
            />
          ) : (
            <ul className="divide-y divide-line/60">
              {openTrades.slice(0, 5).map((trade) => {
                const entry = Number(trade.entryPrice);
                const live = priceOf.get(trade.coinId) ?? entry;
                const move = entry === 0 ? 0 : ((live - entry) / entry) * 100;
                const inFavour =
                  trade.direction === "long" ? live >= entry : live < entry;
                return (
                  <li key={trade.id} className="flex items-center justify-between gap-3 px-6 py-3.5">
                    <div className="min-w-0">
                      <p className="text-sm font-semibold text-white">
                        {trade.symbol}{" "}
                        <span
                          className={`text-[11px] font-bold uppercase ${
                            trade.direction === "long" ? "text-accent" : "text-[#f6465d]"
                          }`}
                        >
                          {trade.direction}
                        </span>
                      </p>
                      <p className="text-[11px] text-[#7c8698]">
                        ${Number(trade.stakeUsd).toFixed(2)} stake · entry $
                        {entry.toFixed(2)}
                      </p>
                    </div>
                    <div className="text-right">
                      <p
                        className={`text-sm font-semibold tabular-nums ${
                          inFavour ? "text-accent" : "text-[#f6465d]"
                        }`}
                      >
                        {move >= 0 ? "+" : ""}
                        {move.toFixed(2)}%
                      </p>
                      <p className="text-[11px] text-[#7c8698]">in your favour</p>
                    </div>
                  </li>
                );
              })}
            </ul>
          )}
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.55fr_1fr]">
        <div className="space-y-6">
          <div className="rounded-2xl border border-line bg-card">
            <div className="flex items-center justify-between border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Holdings</h2>
              <Link
                href="/dashboard/portfolio"
                className="text-xs font-semibold text-brand hover:underline"
              >
                Full portfolio
              </Link>
            </div>
            {portfolio.positions.length === 0 ? (
              <EmptyState
                title="No positions yet"
                body="Use your available balance to buy crypto and the portfolio view will populate."
                action="Open the trading desk"
                href="/dashboard/markets"
              />
            ) : (
              <div className="divide-y divide-line/60">
                {portfolio.positions.slice(0, 5).map((p) => (
                  <div key={p.coinId} className="flex items-center gap-4 px-6 py-4">
                    <CoinBadge symbol={p.symbol} color={p.color} />
                    <div className="min-w-0 flex-1">
                      <p className="font-semibold text-white">{p.name}</p>
                      <p className="text-xs tabular-nums text-[#7c8698]">
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
                        className={`text-xs font-semibold ${
                          p.change24h >= 0 ? "text-accent" : "text-[#f6465d]"
                        }`}
                      >
                        {formatPct(p.change24h)}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          <div className="rounded-2xl border border-line bg-card">
            <div className="flex items-center justify-between border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Active plans</h2>
              <Link
                href="/dashboard/investments"
                className="text-xs font-semibold text-brand hover:underline"
              >
                Manage
              </Link>
            </div>
            {activePlans.length === 0 ? (
              <EmptyState
                title="No active plans"
                body="Plans lock capital for a fixed term and credit a daily accrual."
                action="Browse plans"
                href="/dashboard/plans"
              />
            ) : (
              <div className="divide-y divide-line/60">
                {activePlans.map((i) => {
                  const progress = Math.min(
                    100,
                    Math.round((i.daysPaid / i.durationDays) * 100),
                  );
                  return (
                    <div key={i.id} className="px-6 py-4">
                      <div className="flex items-center justify-between gap-3">
                        <div>
                          <p className="text-sm font-semibold text-white">{i.planName}</p>
                          <p className="text-xs text-[#7c8698]">
                            ${Number(i.principalUsd).toLocaleString()} ·{" "}
                            {(i.annualRateBp / 100).toFixed(2)}% sim. annual
                          </p>
                        </div>
                        <div className="text-right">
                          <p className="text-sm font-semibold tabular-nums text-accent">
                            +{formatUsd(Number(i.earnedUsd))}
                          </p>
                          <p className="text-[11px] text-[#7c8698]">
                            day {i.daysPaid}/{i.durationDays}
                          </p>
                        </div>
                      </div>
                      <div className="mt-2 h-1.5 overflow-hidden rounded-full bg-panel-2">
                        <div
                          className="h-full rounded-full bg-gradient-to-r from-brand to-brand-2"
                          style={{ width: `${progress}%` }}
                        />
                      </div>
                    </div>
                  );
                })}
              </div>
            )}
          </div>
        </div>

        <div className="space-y-6">
          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="mb-4 font-display font-bold">Allocation</h2>
            <PortfolioDonut positions={portfolio.positions} cash={cash + locked} />
          </div>

          <div className="rounded-2xl border border-line bg-card">
            <div className="flex items-center justify-between border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Recent activity</h2>
              <Link
                href="/dashboard/transactions"
                className="text-xs font-semibold text-brand hover:underline"
              >
                All
              </Link>
            </div>
            {activity.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">No activity yet.</p>
            ) : (
              <div className="divide-y divide-line/60">
                {activity.map((a) => (
                  <div key={`${a.type}-${a.id}`} className="px-6 py-3.5">
                    <div className="flex items-center justify-between text-sm">
                      <span className="font-semibold text-white">{a.label}</span>
                      <span
                        className={`tabular-nums ${
                          a.direction === "in" ? "text-accent" : "text-[#c7cedb]"
                        }`}
                      >
                        {a.direction === "in" ? "+" : ""}
                        {formatUsd(a.usd)}
                      </span>
                    </div>
                    <p className="text-[11px] text-[#7c8698]">
                      {new Date(a.createdAt).toLocaleDateString("en-US")}
                      {a.detail ? ` · ${a.detail}` : ""}
                    </p>
                  </div>
                ))}
              </div>
            )}
          </div>

          <MarketNews />

          <div className="rounded-2xl border border-line bg-card">
            <div className="flex items-center justify-between border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Notifications</h2>
              <Link
                href="/dashboard/notifications"
                className="text-xs font-semibold text-brand hover:underline"
              >
                All
              </Link>
            </div>
            {notes.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">Nothing new.</p>
            ) : (
              <div className="divide-y divide-line/60">
                {notes.map((n) => (
                  <div key={n.id} className="px-6 py-3.5">
                    <div className="flex items-start justify-between gap-2">
                      <p className="text-sm font-semibold text-white">{n.title}</p>
                      {!n.readAt && <StatusPill status="pending" />}
                    </div>
                    <p className="mt-1 text-xs text-[#9aa3b2]">{n.body}</p>
                  </div>
                ))}
              </div>
            )}
          </div>

          <div className="rounded-2xl border border-line bg-panel-2 p-5 text-xs text-[#7c8698]">
            Ledger snapshot: cash ${centsToUsd(balances.cash)} · locked $
            {centsToUsd(balances.locked)} · crypto at cost $
            {centsToUsd(balances.crypto)}
          </div>
        </div>
      </div>
    </div>
  );
}
