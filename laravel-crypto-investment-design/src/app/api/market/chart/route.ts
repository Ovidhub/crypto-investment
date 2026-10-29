import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";

/** Timeframes the chart offers, mapped to CoinGecko's `days` parameter. */
export const RANGES: Record<string, number> = {
  "1D": 1,
  "7D": 7,
  "30D": 30,
  "90D": 90,
  "1Y": 365,
};

type GeckoChart = { prices?: Array<[number, number]> };

export async function GET(req: Request) {
  const url = new URL(req.url);
  const coin = (url.searchParams.get("coin") ?? "bitcoin").trim().toLowerCase();
  const range = (url.searchParams.get("range") ?? "1D").toUpperCase();

  // Only a CoinGecko id ever reaches the upstream URL.
  if (!/^[a-z0-9-]{1,60}$/.test(coin)) {
    return NextResponse.json({ ok: false, error: "Unknown coin." }, { status: 400 });
  }
  const days = RANGES[range];
  if (!days) {
    return NextResponse.json({ ok: false, error: "Unknown range." }, { status: 400 });
  }

  try {
    const response = await fetch(
      `https://api.coingecko.com/api/v3/coins/${coin}/market_chart?vs_currency=usd&days=${days}`,
      {
        headers: { accept: "application/json" },
        next: { revalidate: 60 },
      },
    );
    if (!response.ok) throw new Error(`gecko ${response.status}`);

    const data = (await response.json()) as GeckoChart;
    const points = (data.prices ?? [])
      .filter((p): p is [number, number] => Array.isArray(p) && p.length === 2)
      .map(([ts, price]) => [ts, Number(price.toFixed(6))] as [number, number]);

    if (points.length < 2) throw new Error("insufficient data");

    const prices = points.map((p) => p[1]);
    const first = prices[0];
    const last = prices[prices.length - 1];

    return NextResponse.json({
      ok: true,
      coin,
      range,
      points,
      current: last,
      changePct: first === 0 ? 0 : ((last - first) / first) * 100,
      high: Math.max(...prices),
      low: Math.min(...prices),
      updatedAt: new Date().toISOString(),
    });
  } catch {
    return NextResponse.json(
      { ok: false, error: "Price history is unavailable right now." },
      { status: 503 },
    );
  }
}
