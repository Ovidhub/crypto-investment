import { db } from "@/db";
import { holdings, transactions } from "@/db/schema";
import { fetchLiveCoins, type Coin } from "@/lib/coins";
import { desc, eq } from "drizzle-orm";

export type PositionView = {
  coinId: string;
  symbol: string;
  name: string;
  amount: number;
  price: number;
  value: number;
  change24h: number;
  color: string;
  sparkline: number[];
};

export type PortfolioSummary = {
  positions: PositionView[];
  holdingsValue: number;
  totalChange24h: number; // weighted %
  coins: Coin[];
};

export async function getPortfolio(userId: number): Promise<PortfolioSummary> {
  const [rows, coins] = await Promise.all([
    db
      .select()
      .from(holdings)
      .where(eq(holdings.userId, userId)),
    fetchLiveCoins(),
  ]);

  const byId = new Map(coins.map((c) => [c.id, c]));
  const positions: PositionView[] = [];
  let holdingsValue = 0;
  let weightedChange = 0;

  for (const h of rows) {
    const amount = Number(h.amount);
    if (amount <= 0.0000001) continue;
    const coin = byId.get(h.coinId);
    if (!coin) continue;
    const value = amount * coin.price;
    holdingsValue += value;
    positions.push({
      coinId: h.coinId,
      symbol: coin.symbol,
      name: coin.name,
      amount,
      price: coin.price,
      value,
      change24h: coin.change24h,
      color: coin.color,
      sparkline: coin.sparkline,
    });
  }

  for (const p of positions) {
    weightedChange += holdingsValue > 0 ? (p.value / holdingsValue) * p.change24h : 0;
  }

  positions.sort((a, b) => b.value - a.value);

  return {
    positions,
    holdingsValue,
    totalChange24h: weightedChange,
    coins,
  };
}

export async function getTransactions(userId: number, limit = 20) {
  return db
    .select()
    .from(transactions)
    .where(eq(transactions.userId, userId))
    .orderBy(desc(transactions.createdAt))
    .limit(limit);
}
