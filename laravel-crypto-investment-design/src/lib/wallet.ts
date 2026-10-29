import { and, desc, eq, sql } from "drizzle-orm";
import { db } from "@/db";
import {
  depositRequests,
  featureFlags,
  transactions,
  withdrawalRequests,
} from "@/db/schema";
import { logAudit, notify } from "@/lib/audit";
import {
  ACCOUNTS,
  accountBalance,
  credit,
  debit,
  entryExistsFor,
  postJournal,
  syncCachedCash,
  userBalances,
} from "@/lib/ledger";
import {
  MAX_AMOUNT_CENTS,
  MIN_DEPOSIT_CENTS,
  MIN_WITHDRAWAL_CENTS,
  centsToUsd,
  parseUsdInput,
  toCents,
} from "@/lib/money";
import { reference } from "@/lib/ids";

export type WalletResult =
  | { ok: true; id?: number; message: string }
  | { ok: false; error: string; status?: number };

export async function flagEnabled(key: string): Promise<boolean> {
  const [row] = await db
    .select({ enabled: featureFlags.enabled })
    .from(featureFlags)
    .where(eq(featureFlags.key, key))
    .limit(1);
  return row ? row.enabled : true;
}

export async function setFlag(key: string, enabled: boolean) {
  await db
    .insert(featureFlags)
    .values({ key: key.slice(0, 60), enabled })
    .onConflictDoUpdate({
      target: featureFlags.key,
      set: { enabled, updatedAt: new Date() },
    });
}

export async function allFlags() {
  return db.select().from(featureFlags).orderBy(featureFlags.key);
}

function limitError(amount: bigint, min: bigint, max: bigint, label: string) {
  if (amount < min) {
    return { ok: false as const, error: `Minimum ${label} is $${centsToUsd(min)}.`, status: 400 };
  }
  if (amount > max) {
    return { ok: false as const, error: `Maximum ${label} is $${centsToUsd(max)}.`, status: 400 };
  }
  return null;
}

/**
 * Request a simulated deposit. No payment rail is involved: the request sits
 * in the admin review queue until an operator approves it, at which point the
 * ledger is credited. This is the only path that increases user cash besides
 * plan accrual and trading.
 */
export async function requestDeposit(
  userId: number,
  rawAmount: string,
  method = "bank_transfer",
): Promise<WalletResult> {
  if (!(await flagEnabled("deposits"))) {
    return { ok: false, error: "Deposits are temporarily disabled.", status: 503 };
  }
  const amount = parseUsdInput(rawAmount);
  if (amount === null) {
    return { ok: false, error: "Enter an amount with up to 2 decimals.", status: 400 };
  }
  const limit = limitError(amount, MIN_DEPOSIT_CENTS, MAX_AMOUNT_CENTS, "deposit");
  if (limit) return limit;

  const [row] = await db
    .insert(depositRequests)
    .values({
      userId,
      amountUsd: centsToUsd(amount),
      method: method.slice(0, 30),
      reference: reference("SIM"),
      status: "pending",
    })
    .returning({ id: depositRequests.id, reference: depositRequests.reference });

  await logAudit({
    actorId: userId,
    action: "deposit.request",
    targetType: "deposit_request",
    targetId: row.id,
    meta: { amount: centsToUsd(amount) },
  });

  return {
    ok: true,
    id: row.id,
    message: `Deposit ${row.reference} submitted for review.`,
  };
}

/**
 * Request a withdrawal. Funds are reserved immediately (moved from available
 * cash to a locked liability) so they cannot be spent while the request is
 * pending, then released or paid out on review.
 */
export async function requestWithdrawal(
  userId: number,
  rawAmount: string,
  destination: string,
): Promise<WalletResult> {
  if (!(await flagEnabled("withdrawals"))) {
    return { ok: false, error: "Withdrawals are temporarily disabled.", status: 503 };
  }
  const amount = parseUsdInput(rawAmount);
  if (amount === null) {
    return { ok: false, error: "Enter an amount with up to 2 decimals.", status: 400 };
  }
  const limit = limitError(amount, MIN_WITHDRAWAL_CENTS, MAX_AMOUNT_CENTS, "withdrawal");
  if (limit) return limit;
  if (destination.trim().length < 6) {
    return { ok: false, error: "Add a destination wallet or account.", status: 400 };
  }

  const balances = await userBalances(userId);
  if (balances.cash < amount) {
    return {
      ok: false,
      error: `Available cash is $${centsToUsd(balances.cash)}.`,
      status: 400,
    };
  }

  const [row] = await db
    .insert(withdrawalRequests)
    .values({
      userId,
      amountUsd: centsToUsd(amount),
      destination: destination.trim().slice(0, 160),
      status: "pending",
    })
    .returning({ id: withdrawalRequests.id });

  // Reserve the funds.
  await postJournal({
    kind: "withdrawal.reserve",
    memo: `Withdrawal #${row.id} requested — funds reserved`,
    refType: "withdrawal_request",
    refId: row.id,
    actorId: userId,
    postings: [
      debit(ACCOUNTS.userCash(userId), amount),
      credit(ACCOUNTS.userLocked(userId), amount),
    ],
  });
  await syncCachedCash(userId);

  await logAudit({
    actorId: userId,
    action: "withdrawal.request",
    targetType: "withdrawal_request",
    targetId: row.id,
    meta: { amount: centsToUsd(amount) },
  });

  return { ok: true, id: row.id, message: "Withdrawal requested. Funds are reserved pending review." };
}

export async function approveDeposit(
  requestId: number,
  admin: { id: number; email: string },
  note?: string,
): Promise<WalletResult> {
  const [row] = await db
    .select()
    .from(depositRequests)
    .where(eq(depositRequests.id, requestId))
    .limit(1);
  if (!row) return { ok: false, error: "Request not found.", status: 404 };
  if (row.status !== "pending") {
    return { ok: false, error: `Request is already ${row.status}.`, status: 409 };
  }
  if (await entryExistsFor("deposit.approve", "deposit_request", requestId)) {
    return { ok: false, error: "This deposit was already posted.", status: 409 };
  }

  const amount = toCents(row.amountUsd);

  await db
    .update(depositRequests)
    .set({
      status: "approved",
      adminNote: note?.slice(0, 500) ?? null,
      reviewedBy: admin.id,
      reviewedAt: new Date(),
    })
    .where(and(eq(depositRequests.id, requestId), eq(depositRequests.status, "pending")));

  await postJournal({
    kind: "deposit.approve",
    memo: `Deposit #${requestId} approved`,
    refType: "deposit_request",
    refId: requestId,
    actorId: admin.id,
    postings: [
      debit(ACCOUNTS.cashAsset, amount),
      credit(ACCOUNTS.userCash(row.userId), amount),
    ],
  });
  await db.insert(transactions).values({
    userId: row.userId,
    type: "deposit",
    usdValue: centsToUsd(amount),
  });
  await syncCachedCash(row.userId);
  await notify({
    userId: row.userId,
    title: "Deposit approved",
    body: `$${centsToUsd(amount)} was credited to your wallet.`,
  });
  await logAudit({
    actorId: admin.id,
    actorEmail: admin.email,
    action: "deposit.approve",
    targetType: "deposit_request",
    targetId: requestId,
    meta: { amount: centsToUsd(amount), userId: row.userId },
  });

  return { ok: true, message: "Deposit approved and credited." };
}

export async function rejectDeposit(
  requestId: number,
  admin: { id: number; email: string },
  note: string,
): Promise<WalletResult> {
  const [row] = await db
    .select()
    .from(depositRequests)
    .where(eq(depositRequests.id, requestId))
    .limit(1);
  if (!row) return { ok: false, error: "Request not found.", status: 404 };
  if (row.status !== "pending") {
    return { ok: false, error: `Request is already ${row.status}.`, status: 409 };
  }
  await db
    .update(depositRequests)
    .set({
      status: "rejected",
      adminNote: note.slice(0, 500),
      reviewedBy: admin.id,
      reviewedAt: new Date(),
    })
    .where(eq(depositRequests.id, requestId));

  await notify({
    userId: row.userId,
    title: "Deposit not approved",
    body: note ? `Reason: ${note}` : "Your deposit request was not approved.",
  });
  await logAudit({
    actorId: admin.id,
    actorEmail: admin.email,
    action: "deposit.reject",
    targetType: "deposit_request",
    targetId: requestId,
    meta: { note },
  });
  return { ok: true, message: "Deposit rejected." };
}

export async function approveWithdrawal(
  requestId: number,
  admin: { id: number; email: string },
  note?: string,
): Promise<WalletResult> {
  const [row] = await db
    .select()
    .from(withdrawalRequests)
    .where(eq(withdrawalRequests.id, requestId))
    .limit(1);
  if (!row) return { ok: false, error: "Request not found.", status: 404 };
  if (row.status !== "pending") {
    return { ok: false, error: `Request is already ${row.status}.`, status: 409 };
  }
  if (await entryExistsFor("withdrawal.approve", "withdrawal_request", requestId)) {
    return { ok: false, error: "This withdrawal was already paid out.", status: 409 };
  }

  const amount = toCents(row.amountUsd);

  await db
    .update(withdrawalRequests)
    .set({
      status: "approved",
      adminNote: note?.slice(0, 500) ?? null,
      reviewedBy: admin.id,
      reviewedAt: new Date(),
    })
    .where(and(eq(withdrawalRequests.id, requestId), eq(withdrawalRequests.status, "pending")));

  // Release the reservation out of the platform.
  await postJournal({
    kind: "withdrawal.approve",
    memo: `Withdrawal #${requestId} paid`,
    refType: "withdrawal_request",
    refId: requestId,
    actorId: admin.id,
    postings: [
      debit(ACCOUNTS.userLocked(row.userId), amount),
      credit(ACCOUNTS.cashAsset, amount),
    ],
  });
  await db.insert(transactions).values({
    userId: row.userId,
    type: "withdraw",
    usdValue: centsToUsd(amount),
  });
  await syncCachedCash(row.userId);
  await notify({
    userId: row.userId,
    title: "Withdrawal approved",
    body: `$${centsToUsd(amount)} was sent to ${row.destination}.`,
  });
  await logAudit({
    actorId: admin.id,
    actorEmail: admin.email,
    action: "withdrawal.approve",
    targetType: "withdrawal_request",
    targetId: requestId,
    meta: { amount: centsToUsd(amount), userId: row.userId },
  });

  return { ok: true, message: "Withdrawal approved." };
}

export async function rejectWithdrawal(
  requestId: number,
  admin: { id: number; email: string },
  note: string,
): Promise<WalletResult> {
  const [row] = await db
    .select()
    .from(withdrawalRequests)
    .where(eq(withdrawalRequests.id, requestId))
    .limit(1);
  if (!row) return { ok: false, error: "Request not found.", status: 404 };
  if (row.status !== "pending") {
    return { ok: false, error: `Request is already ${row.status}.`, status: 409 };
  }

  const amount = toCents(row.amountUsd);
  await db
    .update(withdrawalRequests)
    .set({
      status: "rejected",
      adminNote: note.slice(0, 500),
      reviewedBy: admin.id,
      reviewedAt: new Date(),
    })
    .where(eq(withdrawalRequests.id, requestId));

  // Return the reserved funds to available cash.
  await postJournal({
    kind: "withdrawal.release",
    memo: `Withdrawal #${requestId} rejected — funds released`,
    refType: "withdrawal_request",
    refId: requestId,
    actorId: admin.id,
    postings: [
      debit(ACCOUNTS.userLocked(row.userId), amount),
      credit(ACCOUNTS.userCash(row.userId), amount),
    ],
  });
  await syncCachedCash(row.userId);
  await notify({
    userId: row.userId,
    title: "Withdrawal not approved",
    body: note ? `Reason: ${note}. Funds returned to your wallet.` : "Funds returned to your wallet.",
  });
  await logAudit({
    actorId: admin.id,
    actorEmail: admin.email,
    action: "withdrawal.reject",
    targetType: "withdrawal_request",
    targetId: requestId,
    meta: { note },
  });

  return { ok: true, message: "Withdrawal rejected; funds returned." };
}

export async function myRequests(userId: number) {
  const [deposits, withdrawals] = await Promise.all([
    db
      .select()
      .from(depositRequests)
      .where(eq(depositRequests.userId, userId))
      .orderBy(desc(depositRequests.createdAt))
      .limit(20),
    db
      .select()
      .from(withdrawalRequests)
      .where(eq(withdrawalRequests.userId, userId))
      .orderBy(desc(withdrawalRequests.createdAt))
      .limit(20),
  ]);
  return { deposits, withdrawals };
}

export async function ledgerCash() {
  return accountBalance(ACCOUNTS.cashAsset);
}
