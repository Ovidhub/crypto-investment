"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";

type CoinOption = { id: string; symbol: string; name: string; price: number; color?: string };

type ChartPayload = {
  ok: boolean;
  points?: Array<[number, number]>;
  current?: number;
  changePct?: number;
  high?: number;
  low?: number;
};

const RANGES = ["1D", "7D", "30D", "90D", "1Y"] as const;
type Range = (typeof RANGES)[number];

const WIDTH = 720;
const FULL_HEIGHT = 260;
const COMPACT_HEIGHT = 190;
const PAD_TOP = 16;
const PAD_BOTTOM = 24;

function money(value: number): string {
  if (!Number.isFinite(value)) return "—";
  if (value >= 1000) return `$${value.toLocaleString("en-US", { maximumFractionDigits: 0 })}`;
  if (value >= 1) return `$${value.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  return `$${value.toLocaleString("en-US", { maximumFractionDigits: 4 })}`;
}

function axisLabel(ts: number, range: Range): string {
  const date = new Date(ts);
  if (range === "1D") {
    return date.toLocaleTimeString("en-US", { hour: "numeric", minute: "2-digit" });
  }
  return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
}

/**
 * Live price chart for the trade page.
 *
 * History comes from our own /api/market/chart route (which reads CoinGecko and
 * caches for a minute); the chart refreshes on the same cadence while the tab
 * is visible, so the line keeps moving without a websocket.
 */
export default function PriceChart({
  coins,
  initialCoinId,
  compact = false,
}: {
  coins: CoinOption[];
  initialCoinId?: string;
  /** Shorter frame for the dashboard overview, where the chart sits beside a ticket. */
  compact?: boolean;
}) {
  const HEIGHT = compact ? COMPACT_HEIGHT : FULL_HEIGHT;
  const [coinId, setCoinId] = useState(initialCoinId ?? coins[0]?.id ?? "bitcoin");
  const [range, setRange] = useState<Range>("1D");
  // Results are keyed by selection, so switching coin or range shows a loading
  // state instead of the previous line, without resetting state in an effect.
  const [result, setResult] = useState<
    { key: string; status: "ready" | "error"; payload: ChartPayload | null } | null
  >(null);
  const [hover, setHover] = useState<{ x: number; price: number; ts: number } | null>(null);
  const svgRef = useRef<SVGSVGElement | null>(null);

  const selectionKey = `${coinId}:${range}`;
  const current = result && result.key === selectionKey ? result : null;
  const data = current?.payload ?? null;
  const status: "loading" | "ready" | "error" = current ? current.status : "loading";

  const coin = coins.find((c) => c.id === coinId) ?? coins[0];

  const load = useCallback(
    async (key: string, coin: string, span: Range, quiet: boolean) => {
      try {
        const response = await fetch(
          `/api/market/chart?coin=${encodeURIComponent(coin)}&range=${span}`,
          { cache: "no-store" },
        );
        const payload = (await response.json()) as ChartPayload;
        if (!response.ok || !payload.ok || !payload.points?.length) {
          throw new Error("no data");
        }
        // A refresh never replaces a chart for a selection the user has left.
        setResult((previous) =>
          previous && previous.key !== key && quiet
            ? previous
            : { key, status: "ready", payload },
        );
      } catch {
        if (!quiet) {
          setResult((previous) =>
            previous && previous.key === key && previous.status === "ready"
              ? previous
              : { key, status: "error", payload: null },
          );
        }
      }
    },
    [],
  );

  useEffect(() => {
    let cancelled = false;
    const run = async () => {
      if (!cancelled) await load(selectionKey, coinId, range, false);
    };
    void run();
    return () => {
      cancelled = true;
    };
  }, [selectionKey, coinId, range, load]);

  // Keep the line live: refresh every 60s while the tab is actually visible.
  useEffect(() => {
    const timer = setInterval(() => {
      if (document.visibilityState === "visible") {
        void load(selectionKey, coinId, range, true);
      }
    }, 60_000);
    return () => clearInterval(timer);
  }, [load, selectionKey, coinId, range]);

  const geometry = useMemo(() => {
    const points = data?.points ?? [];
    if (points.length < 2) return null;

    const prices = points.map((p) => p[1]);
    const min = Math.min(...prices);
    const max = Math.max(...prices);
    const span = max - min || Math.max(max * 0.001, 0.0001);
    const plotHeight = HEIGHT - PAD_TOP - PAD_BOTTOM;

    const x = (i: number) => (i / (points.length - 1)) * WIDTH;
    const y = (price: number) =>
      PAD_TOP + plotHeight - ((price - min) / span) * plotHeight;

    const line = points.map((p, i) => `${i === 0 ? "M" : "L"}${x(i).toFixed(2)},${y(p[1]).toFixed(2)}`).join(" ");
    const area = `${line} L${WIDTH},${HEIGHT - PAD_BOTTOM} L0,${HEIGHT - PAD_BOTTOM} Z`;

    return { points, min, max, x, y, line, area };
  }, [data, HEIGHT]);

  const up = (data?.changePct ?? 0) >= 0;
  const stroke = up ? "#0ecb81" : "#f6465d";
  const fillId = `chartFill-${compact ? "compact" : "full"}`;

  function onMove(event: React.PointerEvent<SVGSVGElement>) {
    if (!geometry || !svgRef.current) return;
    const rect = svgRef.current.getBoundingClientRect();
    const ratio = Math.min(1, Math.max(0, (event.clientX - rect.left) / rect.width));
    const index = Math.round(ratio * (geometry.points.length - 1));
    const point = geometry.points[index];
    if (!point) return;
    setHover({ x: geometry.x(index), price: point[1], ts: point[0] });
  }

  return (
    <div className="rounded-2xl border border-line bg-card">
      <div className="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-4">
        <div className="flex items-center gap-3">
          <span
            className="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white"
            style={{ background: coin?.color ?? "#8b93a7" }}
          >
            {coin?.symbol?.slice(0, 3) ?? "?"}
          </span>
          <div>
            <select
              value={coinId}
              onChange={(event) => setCoinId(event.target.value)}
              aria-label="Coin"
              className="rounded-lg border border-line bg-panel-2 px-2 py-1 text-sm font-semibold text-white outline-none focus:border-brand"
            >
              {coins.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name} ({c.symbol})
                </option>
              ))}
            </select>
            <p className="mt-1 text-[11px] text-[#7c8698]">
              {compact ? "Live chart" : "Live price history"} · CoinGecko
            </p>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-4">
          <div className="text-right">
            <p className="font-display text-xl font-bold tabular-nums text-white">
              {money(data?.current ?? coin?.price ?? 0)}
            </p>
            <p
              className={`text-xs font-semibold tabular-nums ${
                up ? "text-accent" : "text-[#f6465d]"
              }`}
            >
              {data ? `${up ? "+" : ""}${data.changePct?.toFixed(2)}% ${range}` : "—"}
            </p>
          </div>
          <div className="flex rounded-lg border border-line p-0.5">
            {RANGES.map((option) => (
              <button
                key={option}
                type="button"
                onClick={() => setRange(option)}
                className={`rounded-md px-2.5 py-1 text-[11px] font-semibold transition ${
                  range === option
                    ? "bg-gradient-to-r from-brand to-brand-2 text-white"
                    : "text-[#9aa3b2] hover:text-white"
                }`}
              >
                {option}
              </button>
            ))}
          </div>
        </div>
      </div>

      <div className="relative px-2 pb-2 pt-3">
        {status === "loading" && (
          <p className="px-4 py-16 text-center text-sm text-[#9aa3b2]">Loading price history…</p>
        )}
        {status === "error" && (
          <div className="px-4 py-16 text-center">
            <p className="text-sm text-[#9aa3b2]">
              Price history is unavailable right now.
            </p>
            <button
              type="button"
              onClick={() => void load(selectionKey, coinId, range, false)}
              className="mt-3 rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-white hover:border-brand"
            >
              Retry
            </button>
          </div>
        )}

        {status === "ready" && geometry && (
          <>
            <svg
              ref={svgRef}
              viewBox={`0 0 ${WIDTH} ${HEIGHT}`}
              style={{ height: HEIGHT }}
              className="w-full touch-none"
              role="img"
              aria-label={`${coin?.name ?? coinId} price chart over ${range}`}
              onPointerMove={onMove}
              onPointerLeave={() => setHover(null)}
            >
              <defs>
                <linearGradient id={fillId} x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor={stroke} stopOpacity="0.28" />
                  <stop offset="100%" stopColor={stroke} stopOpacity="0" />
                </linearGradient>
              </defs>

              {[0, 0.25, 0.5, 0.75, 1].map((step) => {
                const y = PAD_TOP + step * (HEIGHT - PAD_TOP - PAD_BOTTOM);
                return (
                  <line
                    key={step}
                    x1={0}
                    x2={WIDTH}
                    y1={y}
                    y2={y}
                    stroke="#1e2430"
                    strokeWidth={1}
                    vectorEffect="non-scaling-stroke"
                  />
                );
              })}

              <path d={geometry.area} fill={`url(#${fillId})`} />
              <path
                d={geometry.line}
                fill="none"
                stroke={stroke}
                strokeWidth={2}
                strokeLinejoin="round"
                strokeLinecap="round"
                vectorEffect="non-scaling-stroke"
              />

              {hover && (
                <g>
                  <line
                    x1={hover.x}
                    x2={hover.x}
                    y1={PAD_TOP}
                    y2={HEIGHT - PAD_BOTTOM}
                    stroke="#4a5468"
                    strokeDasharray="3 3"
                    vectorEffect="non-scaling-stroke"
                  />
                  <circle cx={hover.x} cy={geometry.y(hover.price)} r={4} fill={stroke} />
                </g>
              )}
            </svg>

            <div className="flex items-center justify-between px-4 text-[11px] text-[#7c8698]">
              <span>{axisLabel(geometry.points[0][0], range)}</span>
              <span>
                Low {money(data?.low ?? geometry.min)} · High {money(data?.high ?? geometry.max)}
              </span>
              <span>{axisLabel(geometry.points[geometry.points.length - 1][0], range)}</span>
            </div>

            {hover && (
              <div className="pointer-events-none absolute right-6 top-4 rounded-lg border border-line bg-panel-2 px-3 py-1.5 text-xs">
                <span className="font-semibold tabular-nums text-white">{money(hover.price)}</span>
                <span className="ml-2 text-[#7c8698]">
                  {new Date(hover.ts).toLocaleString("en-US", {
                    month: "short",
                    day: "numeric",
                    hour: "numeric",
                    minute: "2-digit",
                  })}
                </span>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
