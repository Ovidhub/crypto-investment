import { and, desc, eq } from "drizzle-orm";
import { db } from "@/db";
import { simTrades, transactions, users } from "@/db/schema";
import { logAudit, notify } from "@/lib/audit";
import { fetchLiveCoins } from "@/lib/coins";
import {
  ACCOUNTS,
  accountBalance,
  credit,
  debit,
  postJournal,
  syncCachedCash,
  userBalances,
} from "@/lib/ledger";
import {
  applyBasisPoints,
  centsToNumber,
  centsToUsd,
  parseUsdInput,
} from "@/lib/money";
import { flagEnabled } from "@/lib/wallet";

/**
 * Simulated fixed-odds trades.
 *
 * A user commits their own simulated cash to a direction on a coin. The stake
 * is escrowed (moved from `liability:user:X:cash` to
 * `liability:user:X:stake`) — it is still their liability, so the platform has
 * not "won" anything yet. Settlement is decided by an operator, or by the
 * explicit "settle at market" action, and every outcome is journaled:
 *
 *   win  → debit stake (release escrow), debit house payout expense, credit cash
 *   loss → debit stake (release escrow), credit house settlement revenue
 *   void → debit stake, credit cash (stake returned untouched)
 *
 * Because the losing side is house revenue and the winning side is house
 * expense, the settlement desk's P&L is visible on the books rather than
 * hidden inside an operator equity balance. No outcome mints money out of
 * nothing.
 */

/** Profit paid on a winning stake: 85% of the amount staked. */
export const SIM_PAYOUT_BP = 8_500;
export const SIM_MIN_STAKE_CENTS = BigInt(1_000); // $10.00
export const SIM_MAX_STAKE_CENTS = BigInt(1_000_000); // $10,000
export const SIM_MAX_OPEN_TRADES = 5;
/** Durations offered in the UI, in minutes. */
export const SIM_DURATIONS = [5, 15, 60, 240] as const;
export const SIM_REASON_MIN = 8;
export const SIM_REASON_MAX = 300;

export type TradeDirection = "long" | "short";
export type TradeStatus = "open" | "won" | "lost" | "void";
export type SettleOutcome = "won" | "lost" | "void" | "market";

export type OpenTradeResult =
  | {
      ok: true;
      tradeId: number;
      message: string;
      entryPrice: number;
      expiresAt: string;
      stake: string;
      maxPayout: string;
    }
  | { ok: false; error: string; status: number };

export type SettleResult =
  | {
      ok: true;
      outcome: Exclude<TradeStatus, "open">;
      source: "operator" | "market";
      message: string;
      payout: string;
      cashAfter: string;
    }
  | { ok: false; error: string; status: number };

export type SimTradeView = {
  id: number;
  userId: number;
  userName: string | null;
  userEmail: string | null;
  coinId: string;
  symbol: string;
  direction: TradeDirection;
  stakeUsd: string;
  entryPrice: string;
  exitPrice: string | null;
  payoutBp: number;
  durationMinutes: number;
  status: TradeStatus;
  outcomeSource: string | null;
  payoutUsd: string | null;
  settleNote: string | null;
  settledBy: number | null;
  openedAt: string;
  expiresAt: string;
  settledAt: string | null;
};

function normalizeDuration(value: number): number | null {
  return (SIM_DURATIONS as readonly number[]).includes(value) ? value : null;
}

function toView(row: {
  trade: typeof simTrades.$inferSelect;
  userName?: string | null;
  userEmail?: string | null;
}): SimTradeView {
  const t = row.trade;
  return {
    id: t.id,
    userId: t.userId,
    userName: row.userName ?? null,
    userEmail: row.userEmail ?? null,
    coinId: t.coinId,
    symbol: t.symbol,
    direction: t.direction === "short" ? "short" : "long",
    stakeUsd: t.stakeUsd,
    entryPrice: t.entryPrice,
    exitPrice: t.exitPrice,
    payoutBp: t.payoutBp,
    durationMinutes: t.durationMinutes,
    status: (t.status as TradeStatus) ?? "open",
    outcomeSource: t.outcomeSource,
    payoutUsd: t.payoutUsd,
    settleNote: t.settleNote,
    settledBy: t.settledBy,
    openedAt: new Date(t.openedAt).toISOString(),
    expiresAt: new Date(t.expiresAt).toISOString(),
    settledAt: t.settledAt ? new Date(t.settledAt).toISOString() : null,
  };
}

/** A user commits their own cash to a direction. */
export async function openSimTrade(input: {
  userId: number;
  coinId: string;
  direction: string;
  rawStake: string;
  durationMinutes: number;
}): Promise<OpenTradeResult> {
  if (!(await flagEnabled("trading"))) {
    return { ok: false, error: "Trading is paused right now.", status: 503 };
  }

  if (input.direction !== "long" && input.direction !== "short") {
    return { ok: false, error: "Choose long or short.", status: 400 };
  }

  const duration = normalizeDuration(input.durationMinutes);
  if (duration === null) {
    return { ok: false, error: "Choose a valid trade duration.", status: 400 };
  }

  const stake = parseUsdInput(input.rawStake);
  if (stake === null) {
    return { ok: false, error: "Enter a stake with up to 2 decimals.", status: 400 };
  }
  if (stake < SIM_MIN_STAKE_CENTS) {
    return { ok: false, error: "Minimum stake is $10.00.", status: 400 };
  }
  if (stake > SIM_MAX_STAKE_CENTS) {
    return {
      ok: false,
      error: `Maximum stake is $${centsToUsd(SIM_MAX_STAKE_CENTS)} per trade.`,
      status: 400,
    };
  }

  const coins = await fetchLiveCoins();
  const coin = coins.find((c) => c.id === input.coinId);
  if (!coin || !(coin.price > 0)) {
    return { ok: false, error: "Unknown coin.", status: 400 };
  }

  // `in`-style filters are limited in the demo driver, so count open rows
  // explicitly rather than asking the database for a set membership test.
  const open = await db
    .select({ id: simTrades.id })
    .from(simTrades)
    .where(and(eq(simTrades.userId, input.userId), eq(simTrades.status, "open")));
  if (open.length >= SIM_MAX_OPEN_TRADES) {
    return {
      ok: false,
      error: `You already have ${SIM_MAX_OPEN_TRADES} open trades. Wait for one to settle.`,
      status: 400,
    };
  }

  const balances = await userBalances(input.userId);
  if (balances.cash < stake) {
    return {
      ok: false,
      error: `Your available cash is $${centsToUsd(balances.cash)} — fund the wallet or lower the stake.`,
      status: 400,
    };
  }

  const expiresAt = new Date(Date.now() + duration * 60 * 1000);
  const direction = input.direction as TradeDirection;

  // Escrow: the stake leaves spendable cash but stays a user liability.
  await postJournal({
    kind: "simtrade.open",
    memo: `${direction === "long" ? "Long" : "Short"} ${coin.symbol} stake of $${centsToUsd(stake)}`,
    refType: "user",
    refId: input.userId,
    actorId: input.userId,
    postings: [
      debit(ACCOUNTS.userCash(input.userId), stake),
      credit(ACCOUNTS.userStake(input.userId), stake),
    ],
  });

  const [created] = await db
    .insert(simTrades)
    .values({
      userId: input.userId,
      coinId: coin.id,
      symbol: coin.symbol,
      direction,
      stakeUsd: centsToUsd(stake),
      entryPrice: coin.price.toFixed(6),
      payoutBp: SIM_PAYOUT_BP,
      durationMinutes: duration,
      status: "open",
      expiresAt,
    })
    .returning({ id: simTrades.id });

  await db.insert(transactions).values({
    userId: input.userId,
    type: "trade_open",
    coinId: coin.id,
    symbol: coin.symbol,
    amount: "0",
    price: coin.price.toFixed(6),
    usdValue: centsToUsd(stake),
    note: `${direction === "long" ? "Long" : "Short"} ${coin.symbol} · stake $${centsToUsd(stake)} · entry $${coin.price.toFixed(2)}`,
  });

  await syncCachedCash(input.userId);
  await logAudit({
    actorId: input.userId,
    action: "simtrade.open",
    targetType: "sim_trade",
    targetId: created?.id ?? null,
    meta: {
      coinId: coin.id,
      direction,
      stake: centsToUsd(stake),
      entryPrice: coin.price,
      durationMinutes: duration,
    },
  });

  const maxPayout = applyBasisPoints(stake, SIM_PAYOUT_BP);
  return {
    ok: true,
    tradeId: created?.id ?? 0,
    message: `${direction === "long" ? "Long" : "Short"} ${coin.symbol} opened at $${coin.price.toFixed(2)}.`,
    entryPrice: coin.price,
    expiresAt: expiresAt.toISOString(),
    stake: centsToUsd(stake),
    maxPayout: centsToUsd(maxPayout),
  };
}

/**
 * Decide an open trade. `outcome: "market"` compares the live price with the
 * entry price (long wins when price rose, short wins when it fell, a tie
 * voids), and is labelled on the customer's receipt as a market settlement
 * rather than an operator decision. Any other outcome is an operator call and
 * is stored with the operator's id and reason.
 */
export async function settleSimTrade(input: {
  admin: { id: number; email: string };
  tradeId: number;
  outcome: SettleOutcome;
  reason: string;
}): Promise<SettleResult> {
  if (
    input.outcome !== "won" &&
    input.outcome !== "lost" &&
    input.outcome !== "void" &&
    input.outcome !== "market"
  ) {
    return { ok: false, error: "Choose won, lost, void or market.", status: 400 };
  }

  const typed = input.reason.trim().replace(/\s+/g, " ");
  // A market settlement needs no operator prose: the outcome speaks for
  // itself, so fall back to the standard note the UI advertises.
  const reason =
    typed || (input.outcome === "market" ? "Settled at the live market price." : "");
  if (reason.length < SIM_REASON_MIN) {
    return {
      ok: false,
      error: `Write a settlement note of at least ${SIM_REASON_MIN} characters — it is shown to the customer.`,
      status: 400,
    };
  }
  if (reason.length > SIM_REASON_MAX) {
    return { ok: false, error: `Keep the note under ${SIM_REASON_MAX} characters.`, status: 400 };
  }

  const [row] = await db
    .select()
    .from(simTrades)
    .where(eq(simTrades.id, input.tradeId))
    .limit(1);
  if (!row) return { ok: false, error: "Trade not found.", status: 404 };
  if (row.status !== "open") {
    return { ok: false, error: "That trade has already been settled.", status: 400 };
  }

  const [owner] = await db
    .select({ id: users.id, name: users.name, email: users.email })
    .from(users)
    .where(eq(users.id, row.userId))
    .limit(1);
  if (!owner) return { ok: false, error: "Trade owner not found.", status: 404 };

  const stake = parseUsdInput(row.stakeUsd) ?? BigInt(0);
  if (stake <= BigInt(0)) {
    return { ok: false, error: "Trade stake is invalid.", status: 400 };
  }

  const coins = await fetchLiveCoins();
  const mark = coins.find((c) => c.id === row.coinId)?.price ?? null;
  const entry = Number(row.entryPrice);

  let outcome: Exclude<TradeStatus, "open">;
  if (input.outcome === "market") {
    if (mark === null) {
      return {
        ok: false,
        error: "Live price unavailable — settle this trade manually.",
        status: 503,
      };
    }
    if (Math.abs(mark - entry) < 1e-9) outcome = "void";
    else {
      const rose = mark > entry;
      const won = row.direction === "short" ? !rose : rose;
      outcome = won ? "won" : "lost";
    }
  } else {
    outcome = input.outcome;
  }

  const payout = outcome === "won" ? applyBasisPoints(stake, row.payoutBp) : BigInt(0);
  const source = input.outcome === "market" ? "market" : "operator";
  const exitPrice = mark !== null ? mark.toFixed(6) : row.entryPrice;

  if (outcome === "won") {
    await postJournal({
      kind: "simtrade.win",
      memo: `Won ${row.symbol} ${row.direction} — stake $${centsToUsd(stake)} + payout $${centsToUsd(payout)}`,
      refType: "sim_trade",
      refId: row.id,
      actorId: input.admin.id,
      postings: [
        debit(ACCOUNTS.userStake(row.userId), stake),
        debit(ACCOUNTS.payoutExpense, payout),
        credit(ACCOUNTS.userCash(row.userId), stake + payout),
      ],
    });
  } else if (outcome === "lost") {
    await postJournal({
      kind: "simtrade.loss",
      memo: `Lost ${row.symbol} ${row.direction} — stake $${centsToUsd(stake)} forfeited`,
      refType: "sim_trade",
      refId: row.id,
      actorId: input.admin.id,
      postings: [
        debit(ACCOUNTS.userStake(row.userId), stake),
        credit(ACCOUNTS.settlementRevenue, stake),
      ],
    });
  } else {
    await postJournal({
      kind: "simtrade.void",
      memo: `Voided ${row.symbol} ${row.direction} — stake $${centsToUsd(stake)} returned`,
      refType: "sim_trade",
      refId: row.id,
      actorId: input.admin.id,
      postings: [
        debit(ACCOUNTS.userStake(row.userId), stake),
        credit(ACCOUNTS.userCash(row.userId), stake),
      ],
    });
  }

  const settledAt = new Date();
  await db
    .update(simTrades)
    .set({
      status: outcome,
      exitPrice,
      payoutUsd: centsToUsd(payout),
      outcomeSource: source,
      settledBy: input.admin.id,
      settleNote: reason,
      settledAt,
    })
    .where(eq(simTrades.id, row.id));

  const type = outcome === "won" ? "trade_win" : outcome === "lost" ? "trade_loss" : "trade_void";
  const usd = outcome === "lost" ? stake : payout > BigInt(0) ? payout : stake;
  const label =
    outcome === "won"
      ? `Won ${row.symbol} ${row.direction} · payout $${centsToUsd(payout)}`
      : outcome === "lost"
        ? `Lost ${row.symbol} ${row.direction} · stake $${centsToUsd(stake)}`
        : `Voided ${row.symbol} ${row.direction} · stake returned`;

  await db.insert(transactions).values({
    userId: row.userId,
    type,
    coinId: row.coinId,
    symbol: row.symbol,
    amount: "0",
    price: exitPrice,
    usdValue: centsToUsd(usd),
    note: `${label} · ${source === "market" ? "settled at market" : "operator decision"}: ${reason}`,
  });

  const cash = await syncCachedCash(row.userId);

  await notify({
    userId: row.userId,
    title: outcome === "won" ? "Trade won" : outcome === "lost" ? "Trade closed at a loss" : "Trade voided",
    body:
      outcome === "won"
        ? `Your ${row.symbol} ${row.direction} won $${centsToUsd(payout)} (stake returned). Settled ${source === "market" ? "at market" : "by an operator"}: ${reason}`
        : outcome === "lost"
          ? `Your ${row.symbol} ${row.direction} lost its $${centsToUsd(stake)} stake. Settled ${source === "market" ? "at market" : "by an operator"}: ${reason}`
          : `Your ${row.symbol} ${row.direction} was voided and the $${centsToUsd(stake)} stake returned. ${reason}`,
  });

  await logAudit({
    actorId: input.admin.id,
    actorEmail: input.admin.email,
    action: `simtrade.settle.${outcome}`,
    targetType: "sim_trade",
    targetId: row.id,
    meta: {
      source,
      stake: centsToUsd(stake),
      payout: centsToUsd(payout),
      entryPrice: row.entryPrice,
      exitPrice,
      reason,
      targetEmail: owner.email,
      cashAfter: centsToUsd(cash),
    },
  });

  return {
    ok: true,
    outcome,
    source,
    message:
      outcome === "won"
        ? `Trade won. Paid $${centsToUsd(payout)} to ${owner.name} (stake $${centsToUsd(stake)} returned).`
        : outcome === "lost"
          ? `Trade lost. ${owner.name} forfeited $${centsToUsd(stake)}.`
          : `Trade voided. $${centsToUsd(stake)} returned to ${owner.name}.`,
    payout: centsToUsd(payout),
    cashAfter: centsToUsd(cash),
  };
}

function joinRows(
  rows: Array<{ trade: typeof simTrades.$inferSelect; userName: string | null; userEmail: string | null }>,
): SimTradeView[] {
  return rows.map((row) => toView(row));
}

export async function listUserTrades(userId: number, limit = 40): Promise<SimTradeView[]> {
  const rows = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(eq(simTrades.userId, userId))
    .orderBy(desc(simTrades.openedAt))
    .limit(limit);
  return joinRows(rows);
}

/** Open trades for the user's page: newest first. */
export async function listOpenTrades(userId: number): Promise<SimTradeView[]> {
  const rows = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(and(eq(simTrades.userId, userId), eq(simTrades.status, "open")))
    .orderBy(desc(simTrades.openedAt))
    .limit(SIM_MAX_OPEN_TRADES);
  return joinRows(rows);
}

/** Every open trade on the desk, oldest expiry first, for the admin queue. */
export async function listDeskTrades(): Promise<SimTradeView[]> {
  const rows = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(eq(simTrades.status, "open"))
    .orderBy(simTrades.expiresAt)
    .limit(100);
  return joinRows(rows);
}

export async function listSettledTrades(limit = 25): Promise<SimTradeView[]> {
  const won = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(eq(simTrades.status, "won"))
    .orderBy(desc(simTrades.settledAt))
    .limit(limit);
  const lost = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(eq(simTrades.status, "lost"))
    .orderBy(desc(simTrades.settledAt))
    .limit(limit);
  const voided = await db
    .select({ trade: simTrades, userName: users.name, userEmail: users.email })
    .from(simTrades)
    .leftJoin(users, eq(users.id, simTrades.userId))
    .where(eq(simTrades.status, "void"))
    .orderBy(desc(simTrades.settledAt))
    .limit(limit);

  return [...won, ...lost, ...voided]
    .map((row) => toView(row))
    .sort(
      (a, b) =>
        new Date(b.settledAt ?? b.openedAt).getTime() -
        new Date(a.settledAt ?? a.openedAt).getTime(),
    )
    .slice(0, limit);
}

/** Desk totals for the admin header. */
export async function simDeskStats() {
  const open = await db
    .select({ stake: simTrades.stakeUsd })
    .from(simTrades)
    .where(eq(simTrades.status, "open"));
  const escrow = open.reduce(
    (sum, row) => sum + centsToNumber(parseUsdInput(row.stake) ?? BigInt(0)),
    0,
  );
  const [paid, taken] = await Promise.all([
    accountBalance(ACCOUNTS.payoutExpense),
    accountBalance(ACCOUNTS.settlementRevenue),
  ]);
  return {
    openCount: open.length,
    escrow,
    paidOut: centsToNumber(paid),
    takenIn: centsToNumber(taken),
    houseNet: centsToNumber(taken - paid),
  };
}
