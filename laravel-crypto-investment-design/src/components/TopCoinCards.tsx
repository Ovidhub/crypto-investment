import type { Coin } from "@/lib/coins";
import { formatUsd, formatPct } from "@/lib/format";
import CoinBadge from "./CoinBadge";

const LABELS = [
  "Highest volume",
  "Top gainer",
  "New listing",
  "Most traded",
  "Biggest gainer",
  "Trending",
];

export default function TopCoinCards({ coins }: { coins: Coin[] }) {
  const picks = coins.slice(0, 6);
  return (
    <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8">
      <div className="mb-8">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-accent">
          Featured crypto coins
        </p>
        <h2 className="mt-3 font-display text-[clamp(1.8rem,3.5vw,2.6rem)] font-bold">
          Top crypto coins updates
        </h2>
      </div>

      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        {picks.map((c, i) => {
          const up = c.change24h >= 0;
          return (
            <div
              key={c.id}
              className="rounded-2xl border border-line bg-card p-4 transition-all hover:-translate-y-1 hover:border-accent/40"
            >
              <p className="text-xs text-[#7c8698]">{LABELS[i]}</p>
              <div className="mt-3 flex items-center gap-2.5">
                <CoinBadge symbol={c.symbol} color={c.color} size={30} />
                <span className="font-semibold text-white">{c.name}</span>
              </div>
              <p className="mt-3 font-display text-lg font-bold tabular-nums text-white">
                {formatUsd(c.price)}
              </p>
              <p
                className={`text-xs font-semibold ${up ? "text-accent" : "text-[#f6465d]"}`}
              >
                {formatPct(c.change24h)}
              </p>
            </div>
          );
        })}
      </div>
    </section>
  );
}
