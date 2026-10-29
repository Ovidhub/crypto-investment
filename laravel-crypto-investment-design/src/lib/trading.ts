import { and, eq } from "drizzle-orm";
import { db } from "@/db";
import { holdings, transactions } from "@/db/schema";
import { logAudit } from "@/lib/audit";
import { fetchLiveCoins } from "@/lib/coins";
import {
  ACCOUNTS,
  credit,
  debit,
  postJournal,
  syncCachedCash,
  userBalances,
} from "@/lib/ledger";
import {
  MAX_AMOUNT_CENTS,
  MIN_DEPOSIT_CENTS,
  applyBasisPoints,
  centsToUsd,
  centsToNumber,
  parseUsdInput,
  toCents,
} from "@/lib/money";
import { flagEnabled } from "@/lib/wallet";

/** 0.50% simulated execution fee, charged on both sides. */
export const TRADE_FEE_BP = 50;

export type TradeResult =
  | { ok: true; message: string; amount: number; price: number; fee: string }
  | { ok: false; error: string; status?: number };

export async function executeTrade(
  userId: number,
  side: "buy" | "sell",
  coinId: string,
  rawAmount: string,
): Promise<TradeResult> {
  if (!(await flagEnabled("trading"))) {
    return { ok: false, error: "Trading is paused.", status: 503 };
  }

  const gross = parseUsdInput(rawAmount);
  if (gross === null) {
    return { ok: false, error: "Enter an amount with up to 2 decimals.", status: 400 };
  }
  if (gross < MIN_DEPOSIT_CENTS) {
    return { ok: false, error: "Minimum trade is $10.00.", status: 400 };
  }
  if (gross > MAX_AMOUNT_CENTS) {
    return { ok: false, error: "Trade exceeds the $1,000,000 limit.", status: 400 };
  }

  const coins = await fetchLiveCoins();
  const coin = coins.find((c) => c.id === coinId);
  if (!coin || !(coin.price > 0)) {
    return { ok: false, error: "Unknown coin.", status: 400 };
  }

  const fee = applyBasisPoints(gross, TRADE_FEE_BP);
  const balances = await userBalances(userId);

  if (side === "buy") {
    const total = gross + fee;
    if (balances.cash < total) {
      return {
        ok: false,
        error: `You need $${centsToUsd(total)} (incl. $${centsToUsd(fee)} fee) but have $${centsToUsd(balances.cash)}.`,
        status: 400,
      };
    }

    // 1) Move the simulated cash: user liability down, custody asset down.
    // 2) Move the coin: custody asset up, user's coin liability up.
    // 3) Book the fee as revenue.
    await postJournal({
      kind: "trade.buy",
      memo: `Buy ${coin.symbol} for $${centsToUsd(gross)}`,
      refType: "coin",
      actorId: userId,
      postings: [
        debit(ACCOUNTS.userCash(userId), gross),
        credit(ACCOUNTS.cashAsset, gross),
      ],
    });
    await postJournal({
      kind: "trade.buy.crypto",
      memo: `Custody ${coin.symbol} acquired for $${centsToUsd(gross)}`,
      refType: "coin",
      actorId: userId,
      postings: [
        debit(ACCOUNTS.cryptoAsset, gross),
        credit(ACCOUNTS.userCrypto(userId), gross),
      ],
    });
    await postJournal({
      kind: "trade.fee",
      memo: `Execution fee on ${coin.symbol} buy`,
      refType: "coin",
      actorId: userId,
      postings: [
        debit(ACCOUNTS.userCash(userId), fee),
        credit(ACCOUNTS.feeRevenue, fee),
      ],
    });

    const quantity = centsToNumber(gross) / coin.price;
    await upsertHolding(userId, coinId, coin.symbol, quantity);

    await db.insert(transactions).values({
      userId,
      type: "buy",
      coinId,
      symbol: coin.symbol,
      amount: quantity.toFixed(10),
      price: coin.price.toFixed(6),
      usdValue: centsToUsd(gross),
    });
    await syncCachedCash(userId);
    await logAudit({
      actorId: userId,
      action: "trade.buy",
      targetType: "coin",
      meta: { coinId, gross: centsToUsd(gross), fee: centsToUsd(fee) },
    });

    return {
      ok: true,
      message: `Bought ${quantity.toFixed(6)} ${coin.symbol}.`,
      amount: quantity,
      price: coin.price,
      fee: centsToUsd(fee),
    };
  }

  // Sell
  const [existing] = await db
    .select()
    .from(holdings)
    .where(and(eq(holdings.userId, userId), eq(holdings.coinId, coinId)))
    .limit(1);
  const held = existing ? Number(existing.amount) : 0;
  const quantity = centsToNumber(gross) / coin.price;
  if (quantity > held + 1e-9) {
    return {
      ok: false,
      error: `You only hold ${held.toFixed(6)} ${coin.symbol}.`,
      status: 400,
    };
  }
  if (balances.crypto < gross && held * coin.price < centsToNumber(gross)) {
    return { ok: false, error: "Position too small to sell.", status: 400 };
  }

  // Cost basis released cannot exceed what the user holds in the ledger.
  const basis = gross < balances.crypto ? gross : balances.crypto;

  await postJournal({
    kind: "trade.sell",
    memo: `Sell ${coin.symbol} for $${centsToUsd(gross)}`,
    refType: "coin",
    actorId: userId,
    postings: [
      debit(ACCOUNTS.userCrypto(userId), basis),
      credit(ACCOUNTS.cryptoAsset, basis),
    ],
  });
  await postJournal({
    kind: "trade.sell.cash",
    memo: `Cash proceeds ${coin.symbol} sale`,
    refType: "coin",
    actorId: userId,
    postings: [
      debit(ACCOUNTS.cashAsset, gross),
      credit(ACCOUNTS.userCash(userId), gross),
    ],
  });
  await postJournal({
    kind: "trade.fee",
    memo: `Execution fee on ${coin.symbol} sale`,
    refType: "coin",
    actorId: userId,
    postings: [
      debit(ACCOUNTS.userCash(userId), fee),
      credit(ACCOUNTS.feeRevenue, fee),
    ],
  });

  await db
    .update(holdings)
    .set({ amount: Math.max(0, held - quantity).toFixed(10) })
    .where(eq(holdings.id, existing.id));

  await db.insert(transactions).values({
    userId,
    type: "sell",
    coinId,
    symbol: coin.symbol,
    amount: quantity.toFixed(10),
    price: coin.price.toFixed(6),
    usdValue: centsToUsd(gross),
  });
  await syncCachedCash(userId);
  await logAudit({
    actorId: userId,
    action: "trade.sell",
    targetType: "coin",
    meta: { coinId, gross: centsToUsd(gross), fee: centsToUsd(fee) },
  });

  return {
    ok: true,
    message: `Sold ${quantity.toFixed(6)} ${coin.symbol} for $${centsToUsd(gross - fee)} net.`,
    amount: quantity,
    price: coin.price,
    fee: centsToUsd(fee),
  };
}

async function upsertHolding(
  userId: number,
  coinId: string,
  symbol: string,
  added: number,
) {
  const [existing] = await db
    .select()
    .from(holdings)
    .where(and(eq(holdings.userId, userId), eq(holdings.coinId, coinId)))
    .limit(1);
  if (existing) {
    await db
      .update(holdings)
      .set({ amount: (Number(existing.amount) + added).toFixed(10) })
      .where(eq(holdings.id, existing.id));
    return;
  }
  await db.insert(holdings).values({
    userId,
    coinId,
    symbol,
    amount: added.toFixed(10),
  });
}

export function tradeCostBasis(prices: Map<string, number>) {
  // Placeholder for phase 3 analytics: average-cost tracking per position.
  return prices;
}

export function usdOf(quantity: number, price: number): string {
  return centsToUsd(toCents((quantity * price).toFixed(2)));
}
