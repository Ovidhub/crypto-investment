import { db } from "@/db";
import { holdings, transactions } from "@/db/schema";
import { fetchLiveCoins, type Coin } from "@/lib/coins";
import { userBalances } from "@/lib/ledger";
import { centsToNumber } from "@/lib/money";
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
  costBasis: number;
  unrealised: number;
};

export type PortfolioSummary = {
  positions: PositionView[];
  holdingsValue: number;
  totalChange24h: number; // weighted %
  coins: Coin[];
  cash: number;
  locked: number;
  investedValue: number;
  totalValue: number;
  cryptoAtCost: number;
};

export async function getPortfolio(userId: number): Promise<PortfolioSummary> {
  const [rows, coins, balances] = await Promise.all([
    db.select().from(holdings).where(eq(holdings.userId, userId)),
    fetchLiveCoins(),
    userBalances(userId),
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
      costBasis: 0,
      unrealised: 0,
    });
  }

  for (const p of positions) {
    weightedChange += holdingsValue > 0 ? (p.value / holdingsValue) * p.change24h : 0;
  }

  positions.sort((a, b) => b.value - a.value);

  const cash = centsToNumber(balances.cash);
  const locked = centsToNumber(balances.locked);
  const investedValue = centsToNumber(balances.locked);
  const cryptoAtCost = centsToNumber(balances.crypto);

  // Allocate the user's recorded cost basis across positions pro rata so the
  // unrealised P/L column is meaningful without per-lot bookkeeping.
  if (cryptoAtCost > 0 && holdingsValue > 0) {
    for (const p of positions) {
      p.costBasis = Number(((p.value / holdingsValue) * cryptoAtCost).toFixed(2));
      p.unrealised = Number((p.value - p.costBasis).toFixed(2));
    }
  }

  return {
    positions,
    holdingsValue,
    totalChange24h: weightedChange,
    coins,
    cash,
    locked,
    investedValue,
    totalValue: holdingsValue + cash + locked,
    cryptoAtCost,
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

export type ActivityItem = {
  id: string;
  type: string;
  label: string;
  detail: string;
  usd: number;
  direction: "in" | "out" | "neutral";
  createdAt: Date;
  symbol?: string | null;
};

const LABELS: Record<string, string> = {
  deposit: "Deposit",
  withdraw: "Withdrawal",
  buy: "Buy",
  sell: "Sell",
  interest: "Simulated yield",
  plan_deposit: "Plan funding",
  plan_release: "Principal released",
  demo_credit: "Demo credit",
};

export async function getActivity(userId: number, limit = 50): Promise<ActivityItem[]> {
  const rows = await getTransactions(userId, limit);
  return rows.map((t) => {
    const usd = Number(t.usdValue);
    const direction: ActivityItem["direction"] =
      t.type === "deposit" || t.type === "interest" || t.type === "demo_credit" || t.type === "sell"
        ? "in"
        : t.type === "buy" || t.type === "withdraw" || t.type === "plan_deposit"
          ? "out"
          : "neutral";
    return {
      id: String(t.id),
      type: t.type,
      label: LABELS[t.type] ?? t.type,
      detail:
        t.symbol && t.amount
          ? `${Number(t.amount).toFixed(6)} ${t.symbol} @ $${Number(t.price ?? 0).toLocaleString("en-US", { maximumFractionDigits: 2 })}`
          : t.type === "demo_credit"
            ? "Simulated funds — not real money"
            : "",
      usd,
      direction,
      createdAt: new Date(t.createdAt),
      symbol: t.symbol,
    };
  });
}
