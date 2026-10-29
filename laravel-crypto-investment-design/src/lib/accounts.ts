import { db, isDemoMode } from "@/db";
import { transactions, users } from "@/db/schema";
import { eq } from "drizzle-orm";
import { ACCOUNTS, credit, debit, postJournal, syncCachedCash } from "@/lib/ledger";
import { hashPassword, verifyPassword } from "@/lib/password";
import { notify } from "@/lib/audit";
import { referralCode } from "@/lib/ids";
import { centsToUsd, toCents } from "@/lib/money";

/** Opening balance credited to a new account when it is created. */
export const OPENING_CREDIT_CENTS = BigInt(1_000_000); // $10,000.00

/** Referral bonus paid to the referrer on a successful signup. */
export const REFERRAL_BONUS_CENTS = BigInt(2_500); // $25.00

export type RegisterInput = {
  name: string;
  email: string;
  password: string;
  referral?: string | null;
};

export type RegisterResult =
  | { ok: true; userId: number }
  | { ok: false; error: string; status: number };

export async function registerUser(input: RegisterInput): Promise<RegisterResult> {
  const email = input.email.trim().toLowerCase().slice(0, 200);
  const name = input.name.trim().slice(0, 120);

  const existing = await db
    .select({ id: users.id })
    .from(users)
    .where(eq(users.email, email))
    .limit(1);
  if (existing.length) {
    return { ok: false, error: "An account with this email already exists.", status: 409 };
  }

  let referredById: number | null = null;
  if (input.referral?.trim()) {
    const [referrer] = await db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.referralCode, input.referral.trim().toUpperCase()))
      .limit(1);
    referredById = referrer?.id ?? null;
  }

  // Retry on the rare referral-code collision instead of failing the signup.
  let created: { id: number } | undefined;
  for (let attempt = 0; attempt < 5 && !created; attempt += 1) {
    try {
      [created] = await db
        .insert(users)
        .values({
          name,
          email,
          passwordHash: hashPassword(input.password),
          role: "user",
          status: "active",
          balanceUsd: "0.00",
          referralCode: referralCode(),
          referredById,
          kycStatus: "unverified",
        })
        .returning({ id: users.id });
    } catch {
      if (attempt === 4) {
        return { ok: false, error: "Registration failed. Please try again.", status: 500 };
      }
    }
  }
  if (!created) {
    return { ok: false, error: "Registration failed. Please try again.", status: 500 };
  }

  // The opening credit is booked as a real journal so the balance shown is
  // always traceable to a ledger entry.
  await postJournal({
    kind: "credit:opening",
    memo: "Opening balance credit",
    refType: "user",
    refId: created.id,
    postings: [
      debit(ACCOUNTS.cashAsset, OPENING_CREDIT_CENTS),
      credit(ACCOUNTS.userCash(created.id), OPENING_CREDIT_CENTS),
    ],
  });
  await db.insert(transactions).values({
    userId: created.id,
    type: "opening_credit",
    usdValue: centsToUsd(OPENING_CREDIT_CENTS),
    note: "Opening balance credited when your account was created",
  });
  await syncCachedCash(created.id);

  if (referredById) {
    // Acquisition bonus: an expense line funding the referrer's wallet.
    // Capped so a self-referral loop can't mint unlimited credit.
    const bonus = REFERRAL_BONUS_CENTS;
    await postJournal({
      kind: "credit:referral",
      memo: "Referral bonus",
      refType: "user",
      refId: created.id,
      postings: [
        debit(ACCOUNTS.referralExpense, bonus),
        credit(ACCOUNTS.userCash(referredById), bonus),
      ],
    });
    await db.insert(transactions).values({
      userId: referredById,
      type: "referral_bonus",
      usdValue: centsToUsd(bonus),
      note: `Referral bonus for ${email}`,
    });
    await syncCachedCash(referredById);
    await notify({
      userId: referredById,
      title: "Referral bonus credited",
      body: `Someone joined with your code. $${centsToUsd(bonus)} was credited to your wallet.`,
    });
  }

  await notify({
    userId: created.id,
    title: "Welcome to Enethi",
    body: "Your account is open. Complete your profile so statements and payouts carry the right details, then fund your wallet to start investing.",
  });

  return { ok: true, userId: created.id };
}

export async function changePassword(
  userId: number,
  currentPassword: string,
  nextPassword: string,
): Promise<{ ok: true } | { ok: false; error: string }> {
  const [row] = await db
    .select({ passwordHash: users.passwordHash })
    .from(users)
    .where(eq(users.id, userId))
    .limit(1);
  if (!row) return { ok: false, error: "Account not found." };
  if (!verifyPassword(currentPassword, row.passwordHash)) {
    return { ok: false, error: "Current password is incorrect." };
  }
  if (nextPassword.length < 10) {
    return { ok: false, error: "New password must be at least 10 characters." };
  }
  await db
    .update(users)
    .set({ passwordHash: hashPassword(nextPassword) })
    .where(eq(users.id, userId));
  return { ok: true };
}

/** Cached cash balance, refreshed from the ledger. */
export async function cashOf(userId: number): Promise<bigint> {
  const { userBalances } = await import("@/lib/ledger");
  const balances = await userBalances(userId);
  return balances.cash;
}

export async function cashString(userId: number): Promise<string> {
  return centsToUsd(await cashOf(userId));
}

export function databaseLabel(): string {
  return isDemoMode ? "in-memory database" : "Postgres";
}

/** Used by the export-data endpoint. */
export async function exportUserData(userId: number) {
  const [profile] = await db
    .select({
      id: users.id,
      name: users.name,
      email: users.email,
      role: users.role,
      status: users.status,
      referralCode: users.referralCode,
      kycStatus: users.kycStatus,
      createdAt: users.createdAt,
    })
    .from(users)
    .where(eq(users.id, userId))
    .limit(1);

  const { userBalances } = await import("@/lib/ledger");
  const balances = await userBalances(userId);

  return {
    profile,
    balances: {
      cashUsd: centsToUsd(balances.cash),
      cryptoAtCostUsd: centsToUsd(balances.crypto),
      lockedUsd: centsToUsd(balances.locked),
      totalUsd: centsToUsd(balances.cash + balances.crypto + balances.locked),
    },
    generatedAt: new Date().toISOString(),
    notice:
      "Exported from your Enethi account. Balances are reported in USD at the time of export.",
  };
}
