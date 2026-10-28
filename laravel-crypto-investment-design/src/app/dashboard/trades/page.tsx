import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { fetchLiveCoins } from "@/lib/coins";
import { formatUsd } from "@/lib/format";
import { userBalances } from "@/lib/ledger";
import { centsToNumber } from "@/lib/money";
import {
  SIM_DURATIONS,
  SIM_MAX_OPEN_TRADES,
  SIM_PAYOUT_BP,
  listUserTrades,
  settleDueTrades,
} from "@/lib/sim-trades";
import SimTradeTicket from "@/components/SimTradeTicket";
import StatCard from "@/components/StatCard";
import StatusPill from "@/components/StatusPill";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

function timeLeft(iso: string): string {
  const ms = new Date(iso).getTime() - Date.now();
  if (ms <= 0) return "due now";
  const minutes = Math.floor(ms / 60000);
  if (minutes < 60) return `${minutes} min left`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours}h ${minutes % 60}m left`;
  return `${Math.floor(hours / 24)}d left`;
}

export default async function TradesPage() {
  const me = await requireUser();

  // A trade whose window has closed settles under any standing policy on this
  // account, so the customer sees a final result instead of a stale open row.
  await settleDueTrades({ userId: me.id });

  const [balances, trades, coins] = await Promise.all([
    userBalances(me.id),
    listUserTrades(me.id, 50),
    fetchLiveCoins(),
  ]);

  const cash = centsToNumber(balances.cash);
  const stake = centsToNumber(balances.stake);
  const open = trades.filter((t) => t.status === "open");
  const settled = trades.filter((t) => t.status !== "open");
  const priceOf = new Map(coins.map((c) => [c.id, c.price]));

  const won = settled.filter((t) => t.status === "won").length;
  const lost = settled.filter((t) => t.status === "lost").length;

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 className="font-display text-2xl font-bold sm:text-3xl">Simulated trades</h1>
          <p className="mt-1 text-sm text-[#9aa3b2]">
            Stake your own simulated cash on a direction. The desk settles each trade
            and the receipt says who decided.
          </p>
        </div>
        <Link
          href="/dashboard/wallet"
          className="rounded-xl border border-line px-4 py-2 text-sm text-[#c7cedb] hover:border-brand hover:text-white"
        >
          Fund wallet
        </Link>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="Available cash" value={formatUsd(cash)} tone="accent" />
        <StatCard
          label="In open trades"
          value={formatUsd(stake)}
          hint={`${open.length} of ${SIM_MAX_OPEN_TRADES} slots used`}
        />
        <StatCard
          label="Record"
          value={`${won}W · ${lost}L`}
          hint={`${settled.length} settled trade${settled.length === 1 ? "" : "s"}`}
        />
        <StatCard
          label="Payout on a win"
          value={`${(SIM_PAYOUT_BP / 100).toFixed(0)}%`}
          hint="of the stake, stake returned"
        />
      </div>

      <div className="grid gap-6 lg:grid-cols-[1fr_1.1fr]">
        <SimTradeTicket
          coins={coins.slice(0, 12).map((c) => ({
            id: c.id,
            symbol: c.symbol,
            name: c.name,
            price: c.price,
          }))}
          cash={cash}
          openCount={open.length}
          maxOpen={SIM_MAX_OPEN_TRADES}
          payoutBp={SIM_PAYOUT_BP}
          durations={SIM_DURATIONS}
        />

        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <div>
              <h2 className="font-display font-bold">Open positions</h2>
              <p className="text-xs text-[#7c8698]">
                Prices refresh when you reload the page.
              </p>
            </div>
            <span className="rounded-full bg-panel-2 px-3 py-1 text-[11px] text-[#9aa3b2]">
              {open.length} open
            </span>
          </div>

          {open.length === 0 ? (
            <EmptyState
              title="No open trades"
              body="Open a trade on the left. Your stake leaves available cash and is held in escrow until the desk settles it."
            />
          ) : (
            <div className="divide-y divide-line/60">
              {open.map((t) => {
                const mark = priceOf.get(t.coinId) ?? Number(t.entryPrice);
                const entry = Number(t.entryPrice);
                const movePct = entry > 0 ? ((mark - entry) / entry) * 100 : 0;
                const winning =
                  t.direction === "short" ? mark < entry : mark > entry;
                return (
                  <div key={t.id} className="px-6 py-4">
                    <div className="flex items-start justify-between gap-3">
                      <div>
                        <p className="font-semibold">
                          <span
                            className={
                              t.direction === "long" ? "text-accent" : "text-[#f6465d]"
                            }
                          >
                            {t.direction === "long" ? "Long" : "Short"}
                          </span>{" "}
                          {t.symbol}
                        </p>
                        <p className="mt-0.5 text-xs text-[#7c8698]">
                          Stake {formatUsd(Number(t.stakeUsd))} · entry{" "}
                          {formatUsd(entry)} · {t.durationMinutes} min
                        </p>
                      </div>
                      <div className="text-right">
                        <p className="font-display font-bold tabular-nums">
                          {formatUsd(mark)}
                        </p>
                        <p
                          className={`text-xs tabular-nums ${
                            winning ? "text-accent" : "text-[#f6465d]"
                          }`}
                        >
                          {movePct >= 0 ? "+" : ""}
                          {movePct.toFixed(2)}% · {timeLeft(t.expiresAt)}
                        </p>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Trade history</h2>
          <p className="text-xs text-[#7c8698]">
            Every settled trade records the outcome, the decision source and the note
            that came with it.
          </p>
        </div>
        {settled.length === 0 ? (
          <EmptyState
            title="Nothing settled yet"
            body="Once the desk closes a trade it appears here with its payout or loss."
          />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[720px] text-sm">
              <thead>
                <tr className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                  <th className="px-6 py-3 font-semibold">Trade</th>
                  <th className="px-6 py-3 font-semibold">Stake</th>
                  <th className="px-6 py-3 font-semibold">Entry → exit</th>
                  <th className="px-6 py-3 font-semibold">Result</th>
                  <th className="px-6 py-3 font-semibold">Decided by</th>
                  <th className="px-6 py-3 font-semibold">Note</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {settled.map((t) => {
                  const result =
                    t.status === "won"
                      ? `+${formatUsd(Number(t.payoutUsd ?? 0))}`
                      : t.status === "lost"
                        ? `−${formatUsd(Number(t.stakeUsd))}`
                        : "Refunded";
                  return (
                    <tr key={t.id} className="align-top">
                      <td className="px-6 py-3">
                        <span className="font-semibold">{t.symbol}</span>
                        <span
                          className={`ml-1.5 text-xs ${
                            t.direction === "long" ? "text-accent" : "text-[#f6465d]"
                          }`}
                        >
                          {t.direction}
                        </span>
                        <p className="text-[11px] text-[#7c8698]">
                          #{t.id} · {new Date(t.settledAt ?? t.openedAt).toLocaleDateString()}
                        </p>
                      </td>
                      <td className="px-6 py-3 tabular-nums">{formatUsd(Number(t.stakeUsd))}</td>
                      <td className="px-6 py-3 text-xs tabular-nums text-[#9aa3b2]">
                        {formatUsd(Number(t.entryPrice))} →{" "}
                        {t.exitPrice ? formatUsd(Number(t.exitPrice)) : "—"}
                      </td>
                      <td
                        className={`px-6 py-3 font-semibold tabular-nums ${
                          t.status === "won"
                            ? "text-accent"
                            : t.status === "lost"
                              ? "text-[#f6465d]"
                              : "text-[#9aa3b2]"
                        }`}
                      >
                        {result}
                        <p className="mt-0.5">
                          <StatusPill status={t.status} />
                        </p>
                      </td>
                      <td className="px-6 py-3 text-xs capitalize text-[#9aa3b2]">
                        {t.outcomeSource ?? "—"}
                      </td>
                      <td className="max-w-[260px] px-6 py-3 text-xs text-[#9aa3b2]">
                        {t.settleNote ?? "—"}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <p className="text-xs leading-relaxed text-[#7c8698]">
        Simulated trading only. No real funds are involved and no outcome is
        guaranteed. Stakes come from your simulated wallet balance; a losing trade
        forfeits the stake, a winning trade returns it plus the stated payout.
      </p>
    </div>
  );
}
