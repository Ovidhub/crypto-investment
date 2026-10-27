import { and, desc, eq, sql } from "drizzle-orm";
import { db } from "@/db";
import {
  auditLog,
  depositRequests,
  investments,
  kycSubmissions,
  messages,
  transactions,
  users,
  waitlist,
  withdrawalRequests,
} from "@/db/schema";

export async function getAdminStats() {
  const [
    [userCount],
    [txnCount],
    [waitCount],
    [volume],
    [cashSum],
    [pendingDeposits],
    [pendingWithdrawals],
    [pendingKyc],
    [activeInvestments],
    [planPrincipal],
  ] = await Promise.all([
    db.select({ c: sql<number>`count(*)::int` }).from(users),
    db.select({ c: sql<number>`count(*)::int` }).from(transactions),
    db.select({ c: sql<number>`count(*)::int` }).from(waitlist),
    db
      .select({ v: sql<number>`coalesce(sum(usd_value),0)::float` })
      .from(transactions),
    db
      .select({ v: sql<number>`coalesce(sum(balance_usd),0)::float` })
      .from(users),
    db
      .select({ c: sql<number>`count(*)::int` })
      .from(depositRequests)
      .where(eq(depositRequests.status, "pending")),
    db
      .select({ c: sql<number>`count(*)::int` })
      .from(withdrawalRequests)
      .where(eq(withdrawalRequests.status, "pending")),
    db
      .select({ c: sql<number>`count(*)::int` })
      .from(kycSubmissions)
      .where(eq(kycSubmissions.status, "pending")),
    db
      .select({ c: sql<number>`count(*)::int` })
      .from(investments)
      .where(eq(investments.status, "active")),
    db
      .select({ v: sql<number>`coalesce(sum(principal_usd),0)::float` })
      .from(investments)
      .where(eq(investments.status, "active")),
  ]);

  return {
    users: userCount.c,
    transactions: txnCount.c,
    waitlist: waitCount.c,
    volume: volume.v,
    cash: cashSum.v,
    pendingDeposits: pendingDeposits.c,
    pendingWithdrawals: pendingWithdrawals.c,
    pendingKyc: pendingKyc.c,
    activeInvestments: activeInvestments.c,
    planPrincipal: planPrincipal.v,
  };
}

export async function getRecentUsers(limit = 100) {
  return db
    .select({
      id: users.id,
      name: users.name,
      email: users.email,
      role: users.role,
      status: users.status,
      balanceUsd: users.balanceUsd,
      kycStatus: users.kycStatus,
      referralCode: users.referralCode,
      createdAt: users.createdAt,
    })
    .from(users)
    .orderBy(desc(users.createdAt))
    .limit(limit);
}

export async function getRecentTransactions(limit = 12) {
  return db
    .select({
      id: transactions.id,
      type: transactions.type,
      symbol: transactions.symbol,
      usdValue: transactions.usdValue,
      createdAt: transactions.createdAt,
      email: users.email,
    })
    .from(transactions)
    .leftJoin(users, sql`${transactions.userId} = ${users.id}`)
    .orderBy(desc(transactions.createdAt))
    .limit(limit);
}

export async function getWaitlist(limit = 200) {
  return db
    .select()
    .from(waitlist)
    .orderBy(desc(waitlist.createdAt))
    .limit(limit);
}

export async function getMessages(limit = 100) {
  return db
    .select()
    .from(messages)
    .orderBy(desc(messages.createdAt))
    .limit(limit);
}

export async function getAuditTrail(limit = 100) {
  return db
    .select()
    .from(auditLog)
    .orderBy(desc(auditLog.createdAt))
    .limit(limit);
}

export async function pendingDepositRows() {
  return db
    .select({
      id: depositRequests.id,
      userId: depositRequests.userId,
      amountUsd: depositRequests.amountUsd,
      status: depositRequests.status,
      reference: depositRequests.reference,
      createdAt: depositRequests.createdAt,
      destination: sql<string | null>`null`,
      userName: users.name,
      userEmail: users.email,
    })
    .from(depositRequests)
    .leftJoin(users, eq(users.id, depositRequests.userId))
    .where(eq(depositRequests.status, "pending"))
    .orderBy(desc(depositRequests.createdAt));
}

export async function pendingWithdrawalRows() {
  return db
    .select({
      id: withdrawalRequests.id,
      userId: withdrawalRequests.userId,
      amountUsd: withdrawalRequests.amountUsd,
      status: withdrawalRequests.status,
      reference: sql<string | null>`null`,
      createdAt: withdrawalRequests.createdAt,
      destination: withdrawalRequests.destination,
      userName: users.name,
      userEmail: users.email,
    })
    .from(withdrawalRequests)
    .leftJoin(users, eq(users.id, withdrawalRequests.userId))
    .where(eq(withdrawalRequests.status, "pending"))
    .orderBy(desc(withdrawalRequests.createdAt));
}

export async function kycQueue() {
  return db
    .select({
      id: kycSubmissions.id,
      userId: kycSubmissions.userId,
      fullName: kycSubmissions.fullName,
      country: kycSubmissions.country,
      documentType: kycSubmissions.documentType,
      documentLast4: kycSubmissions.documentLast4,
      status: kycSubmissions.status,
      createdAt: kycSubmissions.createdAt,
      userEmail: users.email,
    })
    .from(kycSubmissions)
    .leftJoin(users, eq(users.id, kycSubmissions.userId))
    .orderBy(desc(kycSubmissions.createdAt));
}

export async function usersWithMeta(limit = 300) {
  return db
    .select({
      id: users.id,
      name: users.name,
      email: users.email,
      role: users.role,
      status: users.status,
      balanceUsd: users.balanceUsd,
      kycStatus: users.kycStatus,
      referralCode: users.referralCode,
      createdAt: users.createdAt,
    })
    .from(users)
    .orderBy(desc(users.createdAt))
    .limit(limit);
}

export async function kycPendingCount() {
  const [row] = await db
    .select({ c: sql<number>`count(*)::int` })
    .from(kycSubmissions)
    .where(and(eq(kycSubmissions.status, "pending")));
  return row.c;
}
