import type { Coin } from "@/lib/coins";
import { formatPct } from "@/lib/format";
import CoinBadge from "./CoinBadge";

import { copy, getSiteContent } from "@/lib/content";

const ICONS = ["🔗", "🚀", "⚙️", "🏦"];

export default async function WhyChoose({ coins }: { coins: Coin[] }) {
  const c = await getSiteContent();
  const REASONS = [1, 2, 3, 4].map((n, i) => ({
    icon: ICONS[i],
    title: copy(c, `home.why.reason${n}`),
  }));
  const holdings = coins.slice(0, 4);
  const avg =
    holdings.reduce((s, c) => s + c.change24h, 0) / (holdings.length || 1);

  return (
    <section id="how" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="grid items-center gap-12 lg:grid-cols-2">
        <div>
          <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
            {copy(c, "home.why.eyebrow")}
          </p>
          <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
            {copy(c, "home.why.title")}
          </h2>
          <p className="mt-4 max-w-md text-sm leading-relaxed text-[#9aa3b2]">
            {copy(c, "home.why.intro")}
          </p>

          <div className="mt-8 grid gap-4 sm:grid-cols-2">
            {REASONS.map((r) => (
              <div
                key={r.title}
                className="flex items-center gap-3 rounded-2xl border border-line bg-card p-4"
              >
                <span className="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-panel-2 text-lg">
                  {r.icon}
                </span>
                <span className="text-sm font-medium text-[#e7ebf2]">
                  {r.title}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Portfolio card */}
        <div className="relative">
          <div className="pointer-events-none absolute -inset-6 -z-10 rounded-[2rem] bg-gradient-to-br from-brand/20 to-accent/10 blur-2xl" />
          <div className="rounded-3xl border border-line bg-panel p-6 glow-brand">
            <div className="flex items-center justify-between">
              <p className="text-sm text-[#9aa3b2]">Your portfolio is up</p>
              <span className="rounded-full bg-accent/15 px-3 py-1 text-sm font-bold text-accent">
                {formatPct(avg)}
              </span>
            </div>

            <div className="mt-6 space-y-3">
              {holdings.map((c) => {
                const up = c.change24h >= 0;
                return (
                  <div
                    key={c.id}
                    className="flex items-center justify-between rounded-2xl border border-line bg-card px-4 py-3"
                  >
                    <div className="flex items-center gap-3">
                      <CoinBadge symbol={c.symbol} color={c.color} size={34} />
                      <div>
                        <p className="font-semibold text-white">{c.name}</p>
                        <p className="text-xs text-[#7c8698]">
                          {c.symbol}/USD
                        </p>
                      </div>
                    </div>
                    <span
                      className={`font-semibold ${up ? "text-accent" : "text-[#f6465d]"}`}
                    >
                      {formatPct(c.change24h)} {up ? "↑" : "↓"}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
