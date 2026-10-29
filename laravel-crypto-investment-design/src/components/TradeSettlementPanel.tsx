"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

type Trade = {
  id: number;
  userId: number;
  userName: string | null;
  userEmail: string | null;
  coinId: string;
  symbol: string;
  direction: "long" | "short";
  stakeUsd: string;
  entryPrice: string;
  payoutBp: number;
  durationMinutes: number;
  expiresAt: string;
  /** Past its expiry window, computed on the server. */
  due: boolean;
};

function money(value: number): string {
  return value.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const OUTCOMES = [
  { key: "won", label: "Win", tone: "accent" },
  { key: "lost", label: "Loss", tone: "loss" },
  { key: "void", label: "Void", tone: "muted" },
  { key: "market", label: "At market", tone: "market" },
] as const;

export default function TradeSettlementPanel({
  trades,
  prices,
}: {
  trades: Trade[];
  prices: Record<string, number>;
}) {
  const router = useRouter();
  const [note, setNote] = useState("");
  const [busy, setBusy] = useState<number | null>(null);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const noteReady = note.trim().length >= 8;

  async function settle(trade: Trade, outcome: (typeof OUTCOMES)[number]["key"]) {
    const reason =
      note.trim().length >= 8 ? note.trim() : "Settled at the live market price at expiry.";
    const verb =
      outcome === "won"
        ? `pay out the win to ${trade.userName ?? "the customer"}`
        : outcome === "lost"
          ? `forfeit ${trade.userName ?? "the customer"}'s stake`
          : outcome === "void"
            ? "void the trade and refund the stake"
            : "settle at the current market price";
    if (
      !window.confirm(
        `Trade #${trade.id} · ${trade.direction} ${trade.symbol} · $${Number(
          trade.stakeUsd,
        ).toFixed(2)} stake\n\nThis will ${verb}.\n\nNote shown to the customer: "${reason}"`,
      )
    ) {
      return;
    }
    setBusy(trade.id);
    setMessage(null);
    try {
      const res = await fetch("/api/admin/trades", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ tradeId: trade.id, outcome, reason }),
      });
      const data = (await res.json()) as { error?: string; message?: string };
      if (!res.ok) {
        setMessage({ tone: "error", text: data.error ?? "Settlement failed." });
      } else {
        setMessage({ tone: "ok", text: data.message ?? "Trade settled." });
        setNote("");
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error. Try again." });
    } finally {
      setBusy(null);
    }
  }

  return (
    <div className="space-y-4">
      <div className="rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display font-bold">Settlement note</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          Stored on the trade, shown to the customer and copied to the audit trail.
          Leave it blank to use the standard “settled at the live market price” note for
          market settlements.
        </p>
        <textarea
          value={note}
          onChange={(e) => setNote(e.target.value)}
          rows={2}
          maxLength={300}
          placeholder="e.g. BTC closed above entry at the 15-minute expiry."
          className="mt-3 w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
        />
        <p className="mt-1 text-[11px] text-[#7c8698]">
          {note.trim().length}/300 {!noteReady && note.length > 0 ? "— need at least 8 characters" : ""}
        </p>

        {message && (
          <p
            className={`mt-3 rounded-xl border px-4 py-2.5 text-sm ${
              message.tone === "ok"
                ? "border-accent/30 bg-accent/5 text-accent"
                : "border-[#f6465d]/30 bg-[#f6465d]/5 text-[#f6465d]"
            }`}
          >
            {message.text}
          </p>
        )}
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Open trades</h2>
          <p className="text-xs text-[#7c8698]">
            Approving a win releases the stake and pays the profit from the house
            payout account. A loss moves the stake to house revenue. Both are balanced
            journals, so the books stay square.
          </p>
        </div>

        {trades.length === 0 ? (
          <p className="px-6 py-10 text-center text-sm text-[#9aa3b2]">
            No open trades on the desk right now.
          </p>
        ) : (
          <div className="divide-y divide-line/60">
            {trades.map((t) => {
              const entry = Number(t.entryPrice);
              const mark = prices[t.coinId] ?? entry;
              const movePct = entry > 0 ? ((mark - entry) / entry) * 100 : 0;
              const inProfit = t.direction === "short" ? mark < entry : mark > entry;
              const profit = (Number(t.stakeUsd) * t.payoutBp) / 10000;
              const due = t.due;

              return (
                <div key={t.id} className="px-6 py-4">
                  <div className="flex flex-wrap items-start justify-between gap-3">
                    <div className="min-w-0">
                      <p className="font-semibold">
                        <span
                          className={
                            t.direction === "long" ? "text-accent" : "text-[#f6465d]"
                          }
                        >
                          {t.direction === "long" ? "Long" : "Short"}
                        </span>{" "}
                        {t.symbol}
                        <span className="ml-2 text-xs font-normal text-[#7c8698]">#{t.id}</span>
                        {due && (
                          <span className="ml-2 rounded-full bg-[#f5a524]/15 px-2 py-0.5 text-[10px] font-semibold text-[#f5a524]">
                            PAST EXPIRY
                          </span>
                        )}
                      </p>
                      <p className="mt-0.5 text-xs text-[#9aa3b2]">
                        {t.userName ? (
                          <>
                            <Link
                              href={`/admin/users/${t.userId}`}
                              className="text-[#c7cedb] hover:text-brand"
                            >
                              {t.userName}
                            </Link>{" "}
                            · {t.userEmail}
                          </>
                        ) : (
                          `User #${t.userId}`
                        )}
                      </p>
                      <p className="mt-1 text-xs text-[#7c8698]">
                        Stake ${money(Number(t.stakeUsd))} · win pays +${money(profit)} ·
                        entry ${money(entry)} · {t.durationMinutes} min ·{" "}
                        {new Date(t.expiresAt).toLocaleString()}
                      </p>
                    </div>

                    <div className="text-right">
                      <p className="font-display font-bold tabular-nums">{money(mark)}</p>
                      <p
                        className={`text-xs tabular-nums ${
                          inProfit ? "text-accent" : "text-[#f6465d]"
                        }`}
                      >
                        {movePct >= 0 ? "+" : ""}
                        {movePct.toFixed(2)}% —{" "}
                        {Math.abs(movePct) < 0.005
                          ? "flat against entry"
                          : inProfit
                            ? `in profit for a ${t.direction}`
                            : `underwater for a ${t.direction}`}
                      </p>
                    </div>
                  </div>

                  <div className="mt-3 flex flex-wrap gap-2">
                    {OUTCOMES.map((o) => {
                      const disabled =
                        busy !== null || (o.key !== "market" && !noteReady);
                      return (
                        <button
                          key={o.key}
                          type="button"
                          disabled={disabled}
                          onClick={() => settle(t, o.key)}
                          className={`rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors disabled:opacity-40 ${
                            o.tone === "accent"
                              ? "bg-accent text-ink"
                              : o.tone === "loss"
                                ? "bg-[#f6465d] text-white"
                                : o.tone === "market"
                                  ? "border border-brand/50 text-brand hover:bg-brand/10"
                                  : "border border-line text-[#c7cedb] hover:bg-panel-2"
                          }`}
                        >
                          {busy === t.id ? "Posting…" : o.label}
                        </button>
                      );
                    })}
                  </div>
                  {!noteReady && (
                    <p className="mt-2 text-[11px] text-[#7c8698]">
                      Add a settlement note above to record an operator decision, or use
                      “At market”.
                    </p>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
}
