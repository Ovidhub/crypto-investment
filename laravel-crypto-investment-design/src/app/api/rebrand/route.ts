import { NextResponse } from "next/server";
import { eq, inArray, sql } from "drizzle-orm";
import { db } from "@/db";
import { siteContent, users } from "@/db/schema";
import { hashPassword } from "@/lib/password";
import { logAudit } from "@/lib/audit";
import { referralCode } from "@/lib/ids";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/**
 * TEMPORARY: moves the seeded accounts from @enethi.app to @actuatrades.com.
 *
 * History lives on the old row id, so when both addresses exist the empty
 * auto-created row is dropped and the original row is renamed. Nothing with
 * ledger activity is ever deleted.
 */
const MOVES = [
  {
    from: "admin@enethi.app",
    to: "admin@actuatrades.com",
    password: "Actuatrades-Admin-7Kq4Rt9wZb2Ls6Vn",
    role: "owner",
    staff: true,
  },
  {
    from: "manager@enethi.app",
    to: "manager@actuatrades.com",
    password: "Actuatrades-Staff-3Pd8Ym5cWq7Rt2Kf",
    role: "admin",
    staff: true,
  },
  {
    from: "amara@enethi.app",
    to: "amara@actuatrades.com",
    password: "Actuatrades-Demo-2026!42",
    role: "user",
    staff: false,
  },
];

async function historyCounts(userId: number) {
  const result = await db.execute(sql`
    select
      (select count(*) from transactions where user_id = ${userId})::int as transactions,
      (select count(*) from holdings where user_id = ${userId})::int as holdings,
      (select count(*) from ledger_accounts where owner_user_id = ${userId})::int as accounts
  `);
  const rows = (result as unknown as { rows?: Array<Record<string, number>> }).rows ?? [];
  return rows[0] ?? { transactions: 0, holdings: 0, accounts: 0 };
}

export async function GET() {
  const origin = "https://actuatrades.com";
  const report: Array<Record<string, unknown>> = [];

  for (const move of MOVES) {
    const [existingNew] = await db
      .select({ id: users.id, createdAt: users.createdAt })
      .from(users)
      .where(eq(users.email, move.to))
      .limit(1);
    const [existingOld] = await db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.email, move.from))
      .limit(1);

    const entry: Record<string, unknown> = { to: move.to };

    // Drop a freshly auto-seeded duplicate only if it never did anything.
    if (existingNew && existingOld) {
      const history = await historyCounts(existingNew.id);
      const empty =
        history.transactions === 0 && history.holdings === 0 && history.accounts === 0;
      if (empty) {
        await db.delete(users).where(eq(users.id, existingNew.id));
        entry.droppedDuplicate = existingNew.id;
      } else {
        entry.keptBoth =
          "the new address already has history — resolve by hand, nothing was deleted";
      }
    }

    const [keeper] = await db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.email, move.from))
      .limit(1);

    if (keeper) {
      await db
        .update(users)
        .set({
          email: move.to,
          passwordHash: hashPassword(move.password),
          role: move.role,
          status: "active",
        })
        .where(eq(users.id, keeper.id));
      entry.renamedFrom = move.from;
      entry.userId = keeper.id;
    } else {
      const [current] = await db
        .select({ id: users.id })
        .from(users)
        .where(eq(users.email, move.to))
        .limit(1);
      if (current) {
        await db
          .update(users)
          .set({
            passwordHash: hashPassword(move.password),
            role: move.role,
            status: "active",
          })
          .where(eq(users.id, current.id));
        entry.passwordReset = current.id;
      } else {
        const [created] = await db
          .insert(users)
          .values({
            name: move.staff ? "Actuatrades Team" : "Amara Obi",
            email: move.to,
            passwordHash: hashPassword(move.password),
            role: move.role,
            status: "active",
            balanceUsd: "0.00",
            referralCode: referralCode(),
            kycStatus: move.staff ? "verified" : "unverified",
            profileCompletedAt: move.staff ? new Date() : null,
          })
          .returning({ id: users.id });
        entry.created = created.id;
      }
    }

    const [check] = await db
      .select({ id: users.id, role: users.role, status: users.status })
      .from(users)
      .where(eq(users.email, move.to))
      .limit(1);
    entry.finalState = check ?? null;

    for (const candidate of [move.to]) {
      const login = await fetch(`${origin}/api/auth/login`, {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ email: candidate, password: move.password }),
      }).catch(() => null);
      entry.loginStatus = login?.status ?? 0;
    }

    report.push(entry);
  }

  // CMS rows saved earlier may still carry the old brand name.
  const savedContent = await db
    .select({ key: siteContent.key, value: siteContent.value })
    .from(siteContent);
  const rewritten: string[] = [];
  for (const row of savedContent) {
    if (!/enethi/i.test(row.value)) continue;
    const value = row.value
      .replace(/@enethi\.app/gi, "@actuatrades.com")
      .replace(/enethi/gi, "Actuatrades");
    await db
      .update(siteContent)
      .set({ value, updatedAt: new Date() })
      .where(eq(siteContent.key, row.key));
    rewritten.push(row.key);
  }

  const strays = await db
    .select({ id: users.id, email: users.email, role: users.role })
    .from(users)
    .where(inArray(users.email, MOVES.map((m) => m.from)));

  await logAudit({
    actorEmail: "rebrand",
    action: "team.create",
    meta: { moved: MOVES.length },
  });

  return NextResponse.json({
    report,
    contentRows: savedContent.length,
    contentRenamed: rewritten,
    oldAddressesRemaining: strays,
  });
}
