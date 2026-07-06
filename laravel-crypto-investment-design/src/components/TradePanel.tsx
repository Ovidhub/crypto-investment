"use client";

import { useRouter } from "next/navigation";
import { useMemo, useState } from "react";
import type { Coin } from "@/lib/coins";
import { formatUsd, formatPct } from "@/lib/format";
import CoinBadge from "./CoinBadge";

type HoldingMap = Record<string, number>;

export default function TradePanel({
  coins,
  cash,
  holdings,
}: {
  coins: Coin[];
  cash: number;
  holdings: HoldingMap;
}) {
  const router = useRouter();
  const [selected, setSelected] = useState<Coin>(coins[0]);
  const [side, setSide] = useState<"buy" | "sell">("buy");
  const [usd, setUsd] = useState("100");
  const [status, setStatus] = useState<"idle" | "loading">("idle");
  const [msg, setMsg] = useState<{ type: "ok" | "err"; text: string } | null>(
    null,
  );
  const [query, setQuery] = useState("");

  const held = holdings[selected.id] ?? 0;
  const heldValue = held * selected.price;
  const coinAmount = Number(usd) / selected.price;

  const filtered = useMemo(
    () =>
      coins.filter(
        (c) =>
          c.name.toLowerCase().includes(query.toLowerCase()) ||
          c.symbol.toLowerCase().includes(query.toLowerCase()),
      ),
    [coins, query],
  );

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setStatus("loading");
    setMsg(null);
    try {
      const res = await fetch("/api/trade", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          type: side,
          coinId: selected.id,
          usdAmount: Number(usd),
        }),
      });
      const data = await res.json();
      if (!res.ok) {
        setMsg({ type: "err", text: data.error ?? "Trade failed." });
        setStatus("idle");
        return;
      }
      setMsg({
        type: "ok",
        text: `${side === "buy" ? "Bought" : "Sold"} ${coinAmount.toFixed(6)} ${selected.symbol}.`,
      });
      setStatus("idle");
      router.refresh();
    } catch {
      setMsg({ type: "err", text: "Network error." });
      setStatus("idle");
    }
  }

  return (
    <div className="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
      {/* Coin list */}
      <div className="rounded-2xl border border-line bg-card">
        <div className="border-b border-line p-4">
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Search coins…"
            className="w-full rounded-xl border border-line bg-panel-2 px-4 py-2.5 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
          />
        </div>
        <div className="max-h-[520px] divide-y divide-line/60 overflow-y-auto">
          {filtered.map((c) => {
            const active = c.id === selected.id;
            return (
              <button
                key={c.id}
                onClick={() => {
                  setSelected(c);
                  setMsg(null);
                }}
                className={`flex w-full items-center gap-3 px-5 py-3.5 text-left transition-colors ${
                  active ? "bg-white/[0.04]" : "hover:bg-white/[0.02]"
                }`}
              >
                <CoinBadge symbol={c.symbol} color={c.color} size={34} />
                <div className="min-w-0 flex-1">
                  <p className="font-semibold text-white">{c.name}</p>
                  <p className="text-xs text-[#7c8698]">{c.symbol}</p>
                </div>
                <div className="text-right">
                  <p className="font-medium tabular-nums text-white">
                    {formatUsd(c.price)}
                  </p>
                  <p
                    className={`text-xs font-semibold ${c.change24h >= 0 ? "text-accent" : "text-[#f6465d]"}`}
                  >
                    {formatPct(c.change24h)}
                  </p>
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* Order ticket */}
      <div className="h-fit rounded-2xl border border-line bg-card p-6">
        <div className="mb-5 flex items-center gap-3">
          <CoinBadge symbol={selected.symbol} color={selected.color} size={40} />
          <div>
            <p className="font-display text-lg font-bold">{selected.name}</p>
            <p className="text-sm text-[#9aa3b2] tabular-nums">
              {formatUsd(selected.price)}
            </p>
          </div>
        </div>

        <div className="mb-4 grid grid-cols-2 gap-2 rounded-xl border border-line bg-panel-2 p-1">
          {(["buy", "sell"] as const).map((s) => (
            <button
              key={s}
              onClick={() => {
                setSide(s);
                setMsg(null);
              }}
              className={`rounded-lg py-2 text-sm font-semibold capitalize transition-colors ${
                side === s
                  ? s === "buy"
                    ? "bg-accent text-ink"
                    : "bg-[#f6465d] text-white"
                  : "text-[#9aa3b2]"
              }`}
            >
              {s}
            </button>
          ))}
        </div>

        <form onSubmit={submit} className="space-y-4">
          <div>
            <label className="mb-1.5 block text-xs text-[#9aa3b2]">
              Amount (USD)
            </label>
            <input
              value={usd}
              onChange={(e) => setUsd(e.target.value.replace(/[^0-9.]/g, ""))}
              inputMode="decimal"
              className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none focus:border-brand"
            />
            <div className="mt-2 flex gap-2">
              {[50, 100, 500, 1000].map((v) => (
                <button
                  key={v}
                  type="button"
                  onClick={() => setUsd(String(v))}
                  className="flex-1 rounded-lg border border-line bg-panel-2 py-1.5 text-xs text-[#c7cedb] hover:border-brand"
                >
                  ${v}
                </button>
              ))}
            </div>
          </div>

          <div className="space-y-1.5 rounded-xl bg-panel-2 p-4 text-sm">
            <div className="flex justify-between">
              <span className="text-[#9aa3b2]">You receive</span>
              <span className="tabular-nums text-white">
                {(Number.isFinite(coinAmount) ? coinAmount : 0).toFixed(6)}{" "}
                {selected.symbol}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#9aa3b2]">
                {side === "buy" ? "Cash available" : `${selected.symbol} held`}
              </span>
              <span className="tabular-nums text-white">
                {side === "buy"
                  ? formatUsd(cash)
                  : `${held.toFixed(6)} (${formatUsd(heldValue)})`}
              </span>
            </div>
          </div>

          {msg && (
            <p
              className={`text-sm ${msg.type === "ok" ? "text-accent" : "text-[#f6465d]"}`}
            >
              {msg.text}
            </p>
          )}

          <button
            type="submit"
            disabled={status === "loading"}
            className={`w-full rounded-xl py-3.5 text-sm font-semibold transition-transform hover:scale-[1.01] disabled:opacity-60 ${
              side === "buy" ? "bg-accent text-ink" : "bg-[#f6465d] text-white"
            }`}
          >
            {status === "loading"
              ? "Processing…"
              : `${side === "buy" ? "Buy" : "Sell"} ${selected.symbol}`}
          </button>
        </form>
      </div>
    </div>
  );
}
