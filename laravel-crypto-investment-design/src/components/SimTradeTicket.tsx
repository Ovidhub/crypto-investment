"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type CoinOption = { id: string; symbol: string; name: string; price: number };

function money(value: number): string {
  return value.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function durationLabel(minutes: number): string {
  if (minutes < 60) return `${minutes} min`;
  const hours = minutes / 60;
  return `${hours} ${hours === 1 ? "hour" : "hours"}`;
}

export default function SimTradeTicket({
  coins,
  cash,
  openCount,
  maxOpen,
  payoutBp,
  durations,
}: {
  coins: CoinOption[];
  cash: number;
  openCount: number;
  maxOpen: number;
  payoutBp: number;
  durations: readonly number[];
}) {
  const router = useRouter();
  const [coinId, setCoinId] = useState(coins[0]?.id ?? "bitcoin");
  const [direction, setDirection] = useState<"long" | "short">("long");
  const [stake, setStake] = useState("");
  const [duration, setDuration] = useState<number>(durations[1] ?? durations[0] ?? 15);
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const coin = coins.find((c) => c.id === coinId) ?? coins[0];
  const parsed = Number(stake);
  const maxStake = Math.min(10000, cash);
  const profit = Number.isFinite(parsed) ? (parsed * payoutBp) / 10000 : 0;
  const atCapacity = openCount >= maxOpen;
  const valid =
    Number.isFinite(parsed) && parsed >= 10 && parsed <= maxStake && !atCapacity && !!coin;

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    if (!coin) return;
    if (
      !window.confirm(
        `${direction === "long" ? "Long" : "Short"} ${coin.symbol} at $${coin.price.toFixed(2)} with a $${parsed.toFixed(2)} stake?\n\nThe stake is escrowed from your available cash until an operator settles the trade.`,
      )
    ) {
      return;
    }
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/trades", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ coinId, direction, stake, durationMinutes: duration }),
      });
      const data = (await res.json()) as {
        error?: string;
        message?: string;
        entryPrice?: number;
        maxPayout?: string;
      };
      if (!res.ok) {
        setMessage({ tone: "error", text: data.error ?? "Could not open the trade." });
      } else {
        setMessage({
          tone: "ok",
          text: `${data.message ?? "Trade opened."} Max payout $${data.maxPayout ?? money(profit)}.`,
        });
        setStake("");
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error. Try again." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <form onSubmit={submit} className="space-y-5 rounded-2xl border border-line bg-card p-6">
      <div>
        <h2 className="font-display font-bold">Open a trade</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          Your stake comes out of your available cash and is held in escrow
          until an operator settles the trade.
        </p>
      </div>

      <label className="block">
        <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Market</span>
        <select
          value={coinId}
          onChange={(e) => setCoinId(e.target.value)}
          className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
        >
          {coins.map((c) => (
            <option key={c.id} value={c.id}>
              {c.name} ({c.symbol}) — ${money(c.price)}
            </option>
          ))}
        </select>
      </label>

      <div className="grid grid-cols-2 gap-2">
        <button
          type="button"
          onClick={() => setDirection("long")}
          className={`rounded-xl border px-4 py-3 text-sm font-semibold transition-colors ${
            direction === "long"
              ? "border-accent/60 bg-accent/10 text-accent"
              : "border-line bg-panel-2 text-[#9aa3b2] hover:text-white"
          }`}
        >
          Long · price up
        </button>
        <button
          type="button"
          onClick={() => setDirection("short")}
          className={`rounded-xl border px-4 py-3 text-sm font-semibold transition-colors ${
            direction === "short"
              ? "border-[#f6465d]/60 bg-[#f6465d]/10 text-[#f6465d]"
              : "border-line bg-panel-2 text-[#9aa3b2] hover:text-white"
          }`}
        >
          Short · price down
        </button>
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Stake (USD)</span>
          <input
            value={stake}
            onChange={(e) => setStake(e.target.value.replace(/[^0-9.]/g, ""))}
            inputMode="decimal"
            placeholder="100.00"
            className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm tabular-nums outline-none focus:border-brand"
          />
          <span className="mt-1 flex gap-1.5 pt-1">
            {[50, 100, 250, 500].map((preset) => (
              <button
                key={preset}
                type="button"
                onClick={() => setStake(String(preset))}
                disabled={preset > maxStake}
                className="rounded-lg border border-line px-2 py-1 text-[11px] text-[#9aa3b2] hover:border-brand hover:text-white disabled:opacity-40"
              >
                ${preset}
              </button>
            ))}
          </span>
        </label>

        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Duration</span>
          <select
            value={duration}
            onChange={(e) => setDuration(Number(e.target.value))}
            className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
          >
            {durations.map((d) => (
              <option key={d} value={d}>
                {durationLabel(d)}
              </option>
            ))}
          </select>
          <span className="mt-2 block rounded-xl border border-line bg-panel-2 px-3 py-2 text-[11px] text-[#9aa3b2]">
            Available cash{" "}
            <span className="font-semibold tabular-nums text-white">${money(cash)}</span>
            <br />
            Open trades {openCount}/{maxOpen}
          </span>
        </label>
      </div>

      <div className="rounded-xl border border-line bg-panel-2 px-4 py-3 text-xs">
        <div className="flex items-center justify-between">
          <span className="text-[#9aa3b2]">Profit if it wins ({(payoutBp / 100).toFixed(0)}%)</span>
          <span className="font-semibold tabular-nums text-accent">
            +${money(profit)}
          </span>
        </div>
        <div className="mt-1 flex items-center justify-between">
          <span className="text-[#9aa3b2]">Returned with stake</span>
          <span className="font-semibold tabular-nums text-white">
            ${money(Number.isFinite(parsed) ? parsed + profit : 0)}
          </span>
        </div>
        <div className="mt-1 flex items-center justify-between">
          <span className="text-[#9aa3b2]">Loss if it loses</span>
          <span className="font-semibold tabular-nums text-[#f6465d]">
            −${money(Number.isFinite(parsed) ? parsed : 0)}
          </span>
        </div>
      </div>

      {atCapacity && (
        <p className="rounded-xl border border-line bg-panel-2 px-4 py-2.5 text-xs text-[#9aa3b2]">
          You have {maxOpen} open trades — the maximum at once. Wait for one to settle
          before opening another.
        </p>
      )}

      {message && (
        <p
          className={`rounded-xl border px-4 py-2.5 text-sm ${
            message.tone === "ok"
              ? "border-accent/30 bg-accent/5 text-accent"
              : "border-[#f6465d]/30 bg-[#f6465d]/5 text-[#f6465d]"
          }`}
        >
          {message.text}
        </p>
      )}

      <button
        disabled={busy || !valid}
        className={`w-full rounded-xl px-5 py-3 text-sm font-semibold text-white disabled:opacity-50 ${
          direction === "long"
            ? "bg-gradient-to-r from-brand to-brand-2"
            : "bg-[#f6465d]"
        }`}
      >
        {busy
          ? "Placing…"
          : direction === "long"
            ? `Long ${coin?.symbol ?? ""} with $${Number.isFinite(parsed) ? money(parsed) : "0.00"}`
            : `Short ${coin?.symbol ?? ""} with $${Number.isFinite(parsed) ? money(parsed) : "0.00"}`}
      </button>

      <p className="text-[11px] leading-relaxed text-[#7c8698]">
        Minimum stake $10.00, maximum $10,000 per trade, at most{" "}
        {maxOpen} open at once. The outcome is decided by the desk — an operator, or
        the explicit “settle at market” action — and is labelled on your receipt so
        you always see who decided.
      </p>
    </form>
  );
}
