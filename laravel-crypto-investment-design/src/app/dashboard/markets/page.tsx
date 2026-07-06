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
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">Trade</h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          Buy and sell crypto instantly at live market prices.
        </p>
      </div>
      <TradePanel
        coins={portfolio.coins}
        cash={Number(me.balanceUsd)}
        holdings={holdingMap}
      />
    </div>
  );
}
