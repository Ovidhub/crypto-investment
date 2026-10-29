import Link from "next/link";
import { getCurrentUser } from "@/lib/auth";
import { getPortfolio } from "@/lib/portfolio";
import TradePanel from "@/components/TradePanel";

export const dynamic = "force-dynamic";

export default async function MarketsPage() {
  const me = (await getCurrentUser())!;
  const portfolio = await getPortfolio(me.id);

  const holdingMap: Record<string, number> = {};
  for (const p of portfolio.positions) holdingMap[p.coinId] = p.amount;

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 className="font-display text-2xl font-bold sm:text-3xl">Spot trading</h1>
          <p className="mt-1 text-sm text-[#9aa3b2]">
            Buy and sell crypto at live market prices with your available balance.
          </p>
        </div>
        <Link
          href="/dashboard/trades"
          className="rounded-xl border border-line px-4 py-2 text-sm text-[#c7cedb] hover:border-brand hover:text-white"
        >
          Trade desk →
        </Link>
      </div>
      <TradePanel
        coins={portfolio.coins}
        cash={Number(me.balanceUsd)}
        holdings={holdingMap}
      />
    </div>
  );
}
