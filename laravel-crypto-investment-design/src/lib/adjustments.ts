import { desc, eq } from "drizzle-orm";
import { db } from "@/db";
import { transactions, users } from "@/db/schema";
import { logAudit, notify } from "@/lib/audit";
import {
  ACCOUNTS,
  credit,
  debit,
  postJournal,
  syncCachedCash,
  userBalances,
} from "@/lib/ledger";
import { centsToUsd, centsToNumber, parseUsdInput } from "@/lib/money";

/**
 * Operator wallet adjustments.
 *
 * Admins legitimately need to correct a balance, issue a goodwill credit or
 * claw back a duplicate payout. What they must not be able to do is invent
 * trading "profit", so this tool is built with hard edges:
 *
 * - the journal is always balanced against `equity:operator-adjustments`,
 *   never against revenue or yield;
 * - the ledger type is `adjustment_credit` / `adjustment_debit`, which is a
 *   distinct row type from `interest`, `buy` or `sell`;
 * - a written reason is mandatory and is stored on the transaction, shown to
 *   the customer and copied to the audit trail;
 * - an admin cannot adjust their own wallet, and a debit can never take a
 *   wallet below zero.
 */

/** $50,000 per single action — large enough to be useful, small enough to be a speed bump. */
export const MAX_ADJUSTMENT_CENTS = BigInt(5_000_000);
export const MIN_ADJUSTMENT_CENTS = BigInt(100); // $1.00

export type AdjustmentResult =
  | { ok: true; message: string; cashAfter: string }
  | { ok: false; error: string; status: number };

export async function adjustWallet(input: {
  admin: { id: number; email: string };
  userId: number;
  direction: "credit" | "debit";
  rawAmount: string;
  reason: string;
}): Promise<AdjustmentResult> {
  const { admin, userId, direction } = input;

  if (userId === admin.id) {
    return {
      ok: false,
      error: "You cannot adjust your own wallet. Ask another admin.",
      status: 400,
    };
  }

  const amount = parseUsdInput(input.rawAmount);
  if (amount === null) {
    return { ok: false, error: "Enter an amount with up to 2 decimals.", status: 400 };
  }
  if (amount < MIN_ADJUSTMENT_CENTS) {
    return { ok: false, error: "Minimum adjustment is $1.00.", status: 400 };
  }
  if (amount > MAX_ADJUSTMENT_CENTS) {
    return {
      ok: false,
      error: `A single adjustment is capped at $${centsToUsd(MAX_ADJUSTMENT_CENTS)}.`,
      status: 400,
    };
  }

  const reason = input.reason.trim().replace(/\s+/g, " ");
  if (reason.length < 8) {
    return {
      ok: false,
      error: "Write a reason of at least 8 characters — it is shown to the customer.",
      status: 400,
    };
  }
  if (reason.length > 300) {
    return { ok: false, error: "Keep the reason under 300 characters.", status: 400 };
  }

  const [target] = await db
    .select({ id: users.id, email: users.email, name: users.name })
    .from(users)
    .where(eq(users.id, userId))
    .limit(1);
  if (!target) {
    return { ok: false, error: "User not found.", status: 404 };
  }

  const balances = await userBalances(userId);
  if (direction === "debit" && balances.cash < amount) {
    return {
      ok: false,
      error: `Available cash is $${centsToUsd(balances.cash)} — cannot debit more than that.`,
      status: 400,
    };
  }

  await postJournal({
    kind: direction === "credit" ? "adjustment.credit" : "adjustment.debit",
    memo: `Operator ${direction} of $${centsToUsd(amount)} — ${reason}`,
    refType: "user",
    refId: userId,
    actorId: admin.id,
    postings:
      direction === "credit"
        ? [
            debit(ACCOUNTS.operatorEquity, amount),
            credit(ACCOUNTS.userCash(userId), amount),
          ]
        : [
            debit(ACCOUNTS.userCash(userId), amount),
            credit(ACCOUNTS.operatorEquity, amount),
          ],
  });

  await db.insert(transactions).values({
    userId,
    type: direction === "credit" ? "adjustment_credit" : "adjustment_debit",
    usdValue: centsToUsd(amount),
    note: reason,
  });

  const cash = await syncCachedCash(userId);

  await notify({
    userId,
    title: direction === "credit" ? "Wallet credited" : "Wallet debited",
    body: `An operator ${direction}ed your wallet by $${centsToUsd(amount)}. Reason: ${reason}`,
  });

  await logAudit({
    actorId: admin.id,
    actorEmail: admin.email,
    action: `wallet.${direction}`,
    targetType: "user",
    targetId: userId,
    meta: {
      amount: centsToUsd(amount),
      reason,
      cashAfter: centsToUsd(cash),
      targetEmail: target.email,
    },
  });

  return {
    ok: true,
    message: `${direction === "credit" ? "Credited" : "Debited"} $${centsToUsd(amount)} on ${target.name}'s wallet.`,
    cashAfter: centsToUsd(cash),
  };
}

export type AdjustmentRow = {
  id: number;
  userId: number;
  userName: string | null;
  userEmail: string | null;
  direction: "credit" | "debit";
  amountUsd: string;
  reason: string | null;
  createdAt: string;
};

/** Recent operator adjustments, newest first. */
export async function listAdjustments(limit = 50): Promise<AdjustmentRow[]> {
  const rows = await db
    .select({
      id: transactions.id,
      userId: transactions.userId,
      type: transactions.type,
      amountUsd: transactions.usdValue,
      note: transactions.note,
      createdAt: transactions.createdAt,
      userName: users.name,
      userEmail: users.email,
    })
    .from(transactions)
    .leftJoin(users, eq(users.id, transactions.userId))
    .where(
      // Drizzle has no `in` helper for varchar columns beyond eq/ne, so match
      // both adjustment types explicitly.
      eq(transactions.type, "adjustment_credit"),
    )
    .orderBy(desc(transactions.createdAt))
    .limit(limit);

  const debits = await db
    .select({
      id: transactions.id,
      userId: transactions.userId,
      type: transactions.type,
      amountUsd: transactions.usdValue,
      note: transactions.note,
      createdAt: transactions.createdAt,
      userName: users.name,
      userEmail: users.email,
    })
    .from(transactions)
    .leftJoin(users, eq(users.id, transactions.userId))
    .where(eq(transactions.type, "adjustment_debit"))
    .orderBy(desc(transactions.createdAt))
    .limit(limit);

  return [...rows, ...debits]
    .sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
    .slice(0, limit)
    .map((row) => ({
      id: row.id,
      userId: row.userId,
      userName: row.userName,
      userEmail: row.userEmail,
      direction: row.type === "adjustment_credit" ? "credit" : "debit",
      amountUsd: row.amountUsd,
      reason: row.note,
      createdAt: new Date(row.createdAt).toISOString(),
    }));
}

/** Total dollars moved by operators in each direction, for the admin header. */
export async function adjustmentTotals() {
  const rows = await listAdjustments(500);
  let credited = 0;
  let debited = 0;
  for (const row of rows) {
    if (row.direction === "credit") credited += centsToNumber(parseUsdInput(row.amountUsd) ?? BigInt(0));
    else debited += centsToNumber(parseUsdInput(row.amountUsd) ?? BigInt(0));
  }
  return { credited, debited, count: rows.length };
}
