"use client";

import { useEffect, useMemo, useState } from "react";
import type { Coin } from "@/lib/coins";
import { formatUsd, formatPct } from "@/lib/format";
import Sparkline from "./Sparkline";
import CoinBadge from "./CoinBadge";

const CATEGORIES = [
  "All",
  "Bitcoin",
  "Ethereum",
  "Layer 1",
  "DeFi",
  "Stablecoin",
  "Meme",
] as const;

export default function MarketTable({ initial }: { initial: Coin[] }) {
  const [coins, setCoins] = useState<Coin[]>(initial);
  const [filter, setFilter] = useState<(typeof CATEGORIES)[number]>("All");
  const [live, setLive] = useState(false);

  useEffect(() => {
    let active = true;
    const load = async () => {
      try {
        const res = await fetch("/api/coins", { cache: "no-store" });
        if (!res.ok) return;
        const data = (await res.json()) as { coins: Coin[] };
        if (active && Array.isArray(data.coins) && data.coins.length) {
          setCoins(data.coins);
          setLive(true);
        }
      } catch {
        /* keep current data */
      }
    };
    load();
    const id = setInterval(load, 60_000);
    return () => {
      active = false;
      clearInterval(id);
    };
  }, []);

  const filtered = useMemo(
    () => (filter === "All" ? coins : coins.filter((c) => c.category === filter)),
    [coins, filter],
  );

  return (
    <section id="markets" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="mb-8 text-center">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
          More than 99+ coins
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4.5vw,3.4rem)] font-bold">
          Popular Cryptocurrencies
        </h2>
        <p className="mt-3 inline-flex items-center gap-2 text-sm text-[#9aa3b2]">
          <span
            className={`h-2 w-2 rounded-full ${live ? "bg-accent" : "bg-yellow-400"}`}
          />
          {live ? "Live market data · refreshes every 60s" : "Indicative prices"}
        </p>
      </div>

      <div className="mb-6 flex flex-wrap justify-center gap-2">
        {CATEGORIES.map((c) => (
          <button
            key={c}
            onClick={() => setFilter(c)}
            className={`rounded-full px-4 py-1.5 text-sm font-medium transition-colors ${
              filter === c
                ? "bg-gradient-to-r from-brand to-brand-2 text-white"
                : "border border-line bg-white/5 text-[#9aa3b2] hover:text-white"
            }`}
          >
            {c}
          </button>
        ))}
      </div>

      <div className="overflow-hidden rounded-3xl border border-line bg-card">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[720px] text-left">
            <thead>
              <tr className="border-b border-line text-xs uppercase tracking-wider text-[#7c8698]">
                <th className="px-6 py-4 font-medium">#</th>
                <th className="px-6 py-4 font-medium">Name</th>
                <th className="px-6 py-4 text-right font-medium">Price</th>
                <th className="px-6 py-4 text-right font-medium">24h</th>
                <th className="hidden px-6 py-4 text-right font-medium md:table-cell">
                  Market Cap
                </th>
                <th className="px-6 py-4 text-center font-medium">Last 7d</th>
                <th className="px-6 py-4 text-right font-medium">Trade</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((c) => {
                const up = c.change24h >= 0;
                return (
                  <tr
                    key={c.id}
                    className="border-b border-line/60 transition-colors last:border-0 hover:bg-white/[0.03]"
                  >
                    <td className="px-6 py-4 text-sm text-[#7c8698]">{c.rank}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <CoinBadge symbol={c.symbol} color={c.color} />
                        <div>
                          <p className="font-semibold text-white">{c.name}</p>
                          <p className="text-xs text-[#7c8698]">{c.symbol}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right font-medium tabular-nums text-white">
                      {formatUsd(c.price)}
                    </td>
                    <td
                      className={`px-6 py-4 text-right font-semibold tabular-nums ${
                        up ? "text-accent" : "text-[#f6465d]"
                      }`}
                    >
                      {formatPct(c.change24h)}
                    </td>
                    <td className="hidden px-6 py-4 text-right tabular-nums text-[#c7cedb] md:table-cell">
                      {formatUsd(c.marketCap, { compact: true })}
                    </td>
                    <td className="px-6 py-4">
                      <div className="mx-auto flex w-[120px] justify-center">
                        <Sparkline data={c.sparkline} up={up} />
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <a
                        href="#join"
                        className="inline-block rounded-full bg-gradient-to-r from-brand to-brand-2 px-4 py-1.5 text-xs font-semibold text-white transition-transform hover:scale-105"
                      >
                        Trade
                      </a>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </section>
  );
}
