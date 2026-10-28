import { and, eq, sql } from "drizzle-orm";
import { db, isDemoMode } from "@/db";
import {
  ledgerAccounts,
  ledgerEntries,
  ledgerTransactions,
  users,
} from "@/db/schema";
import { centsToUsd, toCents, type Cents } from "@/lib/money";

/**
 * Chart of accounts.
 *
 * - Assets / expenses are debit-normal.
 * - Liabilities / revenue / equity are credit-normal.
 *
 * Client money is never an asset of the platform; it is a liability we owe
 * back to the user. Simulated yield is booked as an expense, which makes it
 * visible on the "how was this funded" screen instead of hiding inside a
 * made-up revenue line.
 */
export const ACCOUNTS = {
  cashAsset: { code: "asset:cash", name: "Simulated custody cash", kind: "asset", normal: "debit" },
  cryptoAsset: { code: "asset:crypto-held", name: "Crypto held for users", kind: "asset", normal: "debit" },
  userCash: ((id: number) => ({
    code: `liability:user:${id}:cash`,
    name: `User ${id} available cash`,
    kind: "liability",
    normal: "credit",
    ownerUserId: id,
  })) as UserAccount,
  userCrypto: ((id: number) => ({
    code: `liability:user:${id}:crypto`,
    name: `User ${id} crypto at cost`,
    kind: "liability",
    normal: "credit",
    ownerUserId: id,
  })) as UserAccount,
  userLocked: ((id: number) => ({
    code: `liability:user:${id}:locked`,
    name: `User ${id} principal in plans`,
    kind: "liability",
    normal: "credit",
    ownerUserId: id,
  })) as UserAccount,
  userStake: ((id: number) => ({
    code: `liability:user:${id}:stake`,
    name: `User ${id} stake in open trades`,
    kind: "liability",
    normal: "credit",
    ownerUserId: id,
  })) as UserAccount,
  feeRevenue: { code: "revenue:fees", name: "Trading fees (simulated)", kind: "revenue", normal: "credit" },
  // Losing stakes are recognised as house income; winning payouts are a house
  // expense. Together they make the settlement desk's P&L legible instead of
  // hiding it inside an operator equity balance.
  settlementRevenue: {
    code: "revenue:trade-settlement",
    name: "Forfeited stakes (simulated)",
    kind: "revenue",
    normal: "credit",
  },
  payoutExpense: {
    code: "expense:trade-payout",
    name: "Winning payouts (simulated)",
    kind: "expense",
    normal: "debit",
  },
  yieldExpense: { code: "expense:simulated-yield", name: "Simulated plan yield", kind: "expense", normal: "debit" },
  referralExpense: { code: "expense:referral-bonus", name: "Referral bonuses (simulated)", kind: "expense", normal: "debit" },
  roundingEquity: { code: "equity:rounding", name: "Rounding", kind: "equity", normal: "credit" },
  // Counter-account for operator credits/debits. An adjustment moves value
  // between platform equity and a user's wallet — never between a user and
  // "profit" — so it cannot masquerade as trading performance.
  operatorEquity: {
    code: "equity:operator-adjustments",
    name: "Operator adjustments",
    kind: "equity",
    normal: "credit",
  },
} as const;

export type AccountSpec = {
  code: string;
  name: string;
  kind: string;
  normal: "debit" | "credit";
  ownerUserId?: number;
};

type UserAccount = (id: number) => AccountSpec;

/**
 * A posting's `delta` is **debit-positive**: a debit to an account is stored as
 * a positive number and a credit as a negative one. Recording every leg on the
 * same axis means the invariant is simply "the deltas sum to zero", and an
 * account's natural balance is the sum flipped for credit-normal accounts.
 *
 * Build legs with `debit()` / `credit()` rather than raw numbers; those helpers
 * reject non-positive amounts, which is where double-entry bugs usually hide.
 */
export type Posting = { account: AccountSpec; delta: Cents };

export function debit(account: AccountSpec, amount: Cents): Posting {
  if (amount <= BigInt(0)) {
    throw new Error(`Debit amount must be positive (${account.code})`);
  }
  return { account, delta: amount };
}

export function credit(account: AccountSpec, amount: Cents): Posting {
  if (amount <= BigInt(0)) {
    throw new Error(`Credit amount must be positive (${account.code})`);
  }
  return { account, delta: -amount };
}

const accountIdCache = new Map<string, number>();

async function accountId(spec: AccountSpec): Promise<number> {
  const cached = accountIdCache.get(spec.code);
  if (cached !== undefined) return cached;

  const [existing] = await db
    .select({ id: ledgerAccounts.id })
    .from(ledgerAccounts)
    .where(eq(ledgerAccounts.code, spec.code))
    .limit(1);

  if (existing) {
    accountIdCache.set(spec.code, existing.id);
    return existing.id;
  }

  const [created] = await db
    .insert(ledgerAccounts)
    .values({
      code: spec.code,
      name: spec.name,
      kind: spec.kind,
      normal: spec.normal,
      ownerUserId: spec.ownerUserId ?? null,
    })
    .returning({ id: ledgerAccounts.id });

  accountIdCache.set(spec.code, created.id);
  return created.id;
}

/** Throws when a journal is unbalanced, has zero legs, or has only one side. */
export function assertBalanced(postings: Posting[]): void {
  if (postings.length < 2) {
    throw new Error("A journal entry needs at least two legs.");
  }
  let net = BigInt(0);
  let hasDebit = false;
  let hasCredit = false;
  for (const posting of postings) {
    if (posting.delta === BigInt(0)) {
      throw new Error(`Zero-value ledger leg on ${posting.account.code}`);
    }
    if (posting.delta > BigInt(0)) hasDebit = true;
    else hasCredit = true;
    net += posting.delta;
  }
  if (!hasDebit || !hasCredit) {
    throw new Error("A journal needs at least one debit and one credit.");
  }
  if (net !== BigInt(0)) {
    throw new Error(
      `Unbalanced journal: debits − credits = ${centsToUsd(net)} (must be 0)`,
    );
  }
}

export type Journal = {
  kind: string;
  memo: string;
  refType?: string;
  refId?: number;
  actorId?: number;
  postings: Posting[];
};

/**
 * Post a balanced journal entry. Returns the ledger transaction id.
 *
 * On a real Postgres connection the whole entry is wrapped in a transaction so
 * a partial write can never leave the books skewed. The in-memory demo
 * database has no transaction support, so it falls back to sequential writes
 * after the balance check has already passed.
 */
export async function postJournal(journal: Journal): Promise<number> {
  assertBalanced(journal.postings);

  const write = async (): Promise<number> => {
    const [txn] = await db
      .insert(ledgerTransactions)
      .values({
        kind: journal.kind,
        memo: journal.memo,
        refType: journal.refType ?? null,
        refId: journal.refId ?? null,
        actorId: journal.actorId ?? null,
      })
      .returning({ id: ledgerTransactions.id });

    for (const posting of journal.postings) {
      const id = await accountId(posting.account);
      await db.insert(ledgerEntries).values({
        txnId: txn.id,
        accountId: id,
        deltaUsd: centsToUsd(posting.delta),
      });
    }
    return txn.id;
  };

  if (isDemoMode) return write();
  return db.transaction(write);
}

/** Balance of an account expressed in its natural direction. */
export async function accountBalance(spec: AccountSpec): Promise<Cents> {
  const id = await accountId(spec);
  const [row] = await db
    .select({
      total: sql<string>`coalesce(sum(delta_usd), 0)::text`,
    })
    .from(ledgerEntries)
    .where(eq(ledgerEntries.accountId, id));
  const signed = toCents(row?.total ?? "0");
  return spec.normal === "debit" ? signed : -signed;
}

/** All user-owned account balances, keyed by account code. */
export async function userBalances(userId: number) {
  const rows = await db
    .select({
      code: ledgerAccounts.code,
      normal: ledgerAccounts.normal,
      total: sql<string>`coalesce(sum(${ledgerEntries.deltaUsd}), 0)::text`,
    })
    .from(ledgerEntries)
    .innerJoin(ledgerAccounts, eq(ledgerAccounts.id, ledgerEntries.accountId))
    .where(eq(ledgerAccounts.ownerUserId, userId))
    .groupBy(ledgerAccounts.code, ledgerAccounts.normal);

  const byCode = new Map(
    rows.map((r) => {
      const signed = toCents(r.total);
      return [r.code, r.normal === "debit" ? signed : -signed];
    }),
  );
  return {
    cash: byCode.get(ACCOUNTS.userCash(userId).code) ?? BigInt(0),
    crypto: byCode.get(ACCOUNTS.userCrypto(userId).code) ?? BigInt(0),
    locked: byCode.get(ACCOUNTS.userLocked(userId).code) ?? BigInt(0),
    // Money committed to open simulated trades. Still the user's, but not
    // spendable until the trade settles.
    stake: byCode.get(ACCOUNTS.userStake(userId).code) ?? BigInt(0),
  };
}

/**
 * Platform-wide trial balance: every account with its natural balance.
 * Used by the admin "books" screen and the ledger verifier.
 */
export async function trialBalance() {
  const rows = await db
    .select({
      code: ledgerAccounts.code,
      name: ledgerAccounts.name,
      kind: ledgerAccounts.kind,
      normal: ledgerAccounts.normal,
      total: sql<string>`coalesce(sum(${ledgerEntries.deltaUsd}), 0)::text`,
    })
    .from(ledgerAccounts)
    .leftJoin(ledgerEntries, eq(ledgerEntries.accountId, ledgerAccounts.id))
    .groupBy(
      ledgerAccounts.id,
      ledgerAccounts.code,
      ledgerAccounts.name,
      ledgerAccounts.kind,
      ledgerAccounts.normal,
    );

  let debits = BigInt(0);
  let credits = BigInt(0);
  const accounts = rows.map((row) => {
    const signed = toCents(row.total);
    const balance = row.normal === "debit" ? signed : -signed;
    if (row.normal === "debit") debits += balance;
    else credits += balance;
    return {
      code: row.code,
      name: row.name,
      kind: row.kind,
      normal: row.normal as "debit" | "credit",
      balance: centsToUsd(balance),
      balanceNumber: Number(centsToUsd(balance)),
    };
  });

  return {
    accounts: accounts.sort((a, b) => a.code.localeCompare(b.code)),
    debits: centsToUsd(debits),
    credits: centsToUsd(credits),
    balanced: debits === credits,
    difference: centsToUsd(debits - credits),
  };
}

/** Latest journals for the admin books screen. */
export async function recentJournals(limit = 40) {
  return db
    .select({
      id: ledgerTransactions.id,
      kind: ledgerTransactions.kind,
      memo: ledgerTransactions.memo,
      createdAt: ledgerTransactions.createdAt,
      actorId: ledgerTransactions.actorId,
    })
    .from(ledgerTransactions)
    .orderBy(sql`${ledgerTransactions.id} desc`)
    .limit(limit);
}

export async function journalLegs(txnId: number) {
  return db
    .select({
      code: ledgerAccounts.code,
      name: ledgerAccounts.name,
      normal: ledgerAccounts.normal,
      delta: ledgerEntries.deltaUsd,
    })
    .from(ledgerEntries)
    .innerJoin(ledgerAccounts, eq(ledgerAccounts.id, ledgerEntries.accountId))
    .where(eq(ledgerEntries.txnId, txnId));
}

/** Mirror the authoritative ledger into users.balance_usd for cheap reads. */
export async function syncCachedCash(userId: number): Promise<Cents> {
  const balances = await userBalances(userId);
  await db
    .update(users)
    .set({ balanceUsd: centsToUsd(balances.cash) })
    .where(eq(users.id, userId));
  return balances.cash;
}

export async function entryExistsFor(txnKind: string, refType: string, refId: number) {
  const [row] = await db
    .select({ id: ledgerTransactions.id })
    .from(ledgerTransactions)
    .where(
      and(
        eq(ledgerTransactions.kind, txnKind),
        eq(ledgerTransactions.refType, refType),
        eq(ledgerTransactions.refId, refId),
      ),
    )
    .limit(1);
  return Boolean(row);
}
