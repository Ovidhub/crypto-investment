import { db } from "@/db";
import { users, transactions, waitlist } from "@/db/schema";
import { sql, desc } from "drizzle-orm";

export async function getAdminStats() {
  const [[userCount], [txnCount], [waitCount], [volume], [cashSum]] =
    await Promise.all([
      db.select({ c: sql<number>`count(*)::int` }).from(users),
      db.select({ c: sql<number>`count(*)::int` }).from(transactions),
      db.select({ c: sql<number>`count(*)::int` }).from(waitlist),
      db
        .select({ v: sql<number>`coalesce(sum(usd_value),0)::float` })
        .from(transactions),
      db
        .select({ v: sql<number>`coalesce(sum(balance_usd),0)::float` })
        .from(users),
    ]);

  return {
    users: userCount.c,
    transactions: txnCount.c,
    waitlist: waitCount.c,
    volume: volume.v,
    cash: cashSum.v,
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
