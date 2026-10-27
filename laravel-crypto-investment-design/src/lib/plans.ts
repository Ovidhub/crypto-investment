import { and, asc, desc, eq, sql } from "drizzle-orm";
import { db } from "@/db";
import { investments, plans, transactions, users } from "@/db/schema";
import { logAudit, notify } from "@/lib/audit";
import {
  ACCOUNTS,
  credit,
  debit,
  postJournal,
  syncCachedCash,
  userBalances,
} from "@/lib/ledger";
import { centsToUsd, centsToNumber, parseUsdInput, toCents } from "@/lib/money";
import { flagEnabled } from "@/lib/wallet";

/**
 * Simulated yield engine.
 *
 * Plans are *simulators*: the daily accrual is computed from the plan's
 * annualised rate and booked as a "simulated yield" expense, not as a claim
 * about what a real market will do. Nothing here is a promise of return, and
 * the UI labels every figure as simulated.
 *
 * Accrual is idempotent per calendar day and per investment, so re-running the
 * cron cannot double-credit a user.
 */

export type PlanResult =
  | { ok: true; message: string; id?: number }
  | { ok: false; error: string; status?: number };

export async function listPlans() {
  return db
    .select()
    .from(plans)
    .where(eq(plans.status, "active"))
    .orderBy(asc(plans.sortOrder), asc(plans.id));
}

export async function getPlanBySlug(slug: string) {
  const [plan] = await db
    .select()
    .from(plans)
    .where(eq(plans.slug, slug))
    .limit(1);
  return plan ?? null;
}

export async function subscribeToPlan(
  userId: number,
  slug: string,
  rawAmount: string,
): Promise<PlanResult> {
  if (!(await flagEnabled("plans"))) {
    return { ok: false, error: "Plans are paused right now.", status: 503 };
  }
  const plan = await getPlanBySlug(slug);
  if (!plan) return { ok: false, error: "Plan not found.", status: 404 };

  const amount = parseUsdInput(rawAmount);
  if (amount === null) {
    return { ok: false, error: "Enter an amount with up to 2 decimals.", status: 400 };
  }
  const min = toCents(plan.minAmountUsd);
  const max = toCents(plan.maxAmountUsd);
  if (amount < min || amount > max) {
    return {
      ok: false,
      error: `${plan.name} accepts $${centsToUsd(min)} – $${centsToUsd(max)}.`,
      status: 400,
    };
  }

  const balances = await userBalances(userId);
  if (balances.cash < amount) {
    return {
      ok: false,
      error: `Available cash is $${centsToUsd(balances.cash)}.`,
      status: 400,
    };
  }

  const endsAt = new Date(Date.now() + plan.durationDays * 86_400_000);

  const [row] = await db
    .insert(investments)
    .values({
      userId,
      planId: plan.id,
      principalUsd: centsToUsd(amount),
      annualRateBp: plan.annualRateBp,
      durationDays: plan.durationDays,
      status: "active",
      endsAt,
    })
    .returning({ id: investments.id });

  // Lock the principal: available cash down, locked liability up.
  await postJournal({
    kind: "plan.subscribe",
    memo: `Principal locked in ${plan.name} (simulation #${row.id})`,
    refType: "investment",
    refId: row.id,
    actorId: userId,
    postings: [
      debit(ACCOUNTS.userCash(userId), amount),
      credit(ACCOUNTS.userLocked(userId), amount),
    ],
  });
  await db.insert(transactions).values({
    userId,
    type: "plan_deposit",
    usdValue: centsToUsd(amount),
  });
  await syncCachedCash(userId);

  await notify({
    userId,
    title: `Simulation started: ${plan.name}`,
    body: `$${centsToUsd(amount)} of simulated capital is locked for ${plan.durationDays} days. Yields are simulated, not guaranteed.`,
  });
  await logAudit({
    actorId: userId,
    action: "plan.subscribe",
    targetType: "investment",
    targetId: row.id,
    meta: { slug: plan.slug, amount: centsToUsd(amount) },
  });

  return {
    ok: true,
    id: row.id,
    message: `${plan.name} simulation started with $${centsToUsd(amount)}.`,
  };
}

export function dailyAccrual(principalCents: bigint, annualRateBp: number): bigint {
  // principal × bp / 10000 / 365, rounded down to the cent.
  return (principalCents * BigInt(annualRateBp)) / (BigInt(10_000) * BigInt(365));
}

/**
 * Accrue every active investment that has not yet been credited today.
 * Returns a per-investment summary for the cron response.
 */
export async function accrueAll(now = new Date()) {
  const active = await db
    .select()
    .from(investments)
    .where(eq(investments.status, "active"));

  const results: Array<{
    id: number;
    userId: number;
    credited: string;
    daysPaid: number;
    completed: boolean;
  }> = [];

  for (const inv of active) {
    if (inv.endsAt && new Date(inv.endsAt).getTime() <= now.getTime()) {
      await completeInvestment(inv.id);
      results.push({
        id: inv.id,
        userId: inv.userId,
        credited: "0.00",
        daysPaid: inv.daysPaid,
        completed: true,
      });
      continue;
    }

    const last = inv.lastAccruedAt ? new Date(inv.lastAccruedAt) : null;
    const alreadyToday =
      last !== null &&
      last.toISOString().slice(0, 10) === now.toISOString().slice(0, 10);
    if (alreadyToday) {
      results.push({
        id: inv.id,
        userId: inv.userId,
        credited: "0.00",
        daysPaid: inv.daysPaid,
        completed: false,
      });
      continue;
    }

    const interest = dailyAccrual(toCents(inv.principalUsd), inv.annualRateBp);
    if (interest <= BigInt(0)) continue;

    await postJournal({
      kind: "plan.accrual",
      memo: `Simulated yield — investment #${inv.id} day ${inv.daysPaid + 1}`,
      refType: "investment",
      refId: inv.id,
      postings: [
        debit(ACCOUNTS.yieldExpense, interest),
        credit(ACCOUNTS.userCash(inv.userId), interest),
      ],
    });

    const daysPaid = inv.daysPaid + 1;
    const earned = toCents(inv.earnedUsd) + interest;
    const finished = daysPaid >= inv.durationDays;
    await db
      .update(investments)
      .set({
        daysPaid,
        earnedUsd: centsToUsd(earned),
        lastAccruedAt: now,
        status: finished ? "completed" : "active",
      })
      .where(eq(investments.id, inv.id));

    await db.insert(transactions).values({
      userId: inv.userId,
      type: "interest",
      usdValue: centsToUsd(interest),
    });
    await syncCachedCash(inv.userId);

    if (finished) {
      await releasePrincipal(inv.id);
    }

    results.push({
      id: inv.id,
      userId: inv.userId,
      credited: centsToUsd(interest),
      daysPaid,
      completed: finished,
    });
  }

  if (results.some((r) => r.credited !== "0.00")) {
    await logAudit({
      action: "plan.accrual.run",
      targetType: "investment",
      meta: { credited: results.filter((r) => r.credited !== "0.00").length },
    });
  }

  return results;
}

async function releasePrincipal(investmentId: number) {
  const [inv] = await db
    .select()
    .from(investments)
    .where(eq(investments.id, investmentId))
    .limit(1);
  if (!inv) return;
  const principal = toCents(inv.principalUsd);
  await postJournal({
    kind: "plan.release",
    memo: `Principal released — investment #${investmentId}`,
    refType: "investment",
    refId: investmentId,
    postings: [
      debit(ACCOUNTS.userLocked(inv.userId), principal),
      credit(ACCOUNTS.userCash(inv.userId), principal),
    ],
  });
  await syncCachedCash(inv.userId);
}

/** Close an investment early: principal back, no further accrual. */
export async function completeInvestment(investmentId: number): Promise<PlanResult> {
  const [inv] = await db
    .select()
    .from(investments)
    .where(eq(investments.id, investmentId))
    .limit(1);
  if (!inv) return { ok: false, error: "Investment not found.", status: 404 };
  if (inv.status !== "active") {
    return { ok: false, error: "Investment is already closed.", status: 409 };
  }
  await db
    .update(investments)
    .set({ status: "completed" })
    .where(and(eq(investments.id, investmentId), eq(investments.status, "active")));
  await releasePrincipal(investmentId);
  await notify({
    userId: inv.userId,
    title: "Simulation finished",
    body: `Your principal of $${centsToUsd(toCents(inv.principalUsd))} was released to your wallet.`,
  });
  return { ok: true, message: "Principal released." };
}

export async function myInvestments(userId: number) {
  return db
    .select({
      id: investments.id,
      principalUsd: investments.principalUsd,
      annualRateBp: investments.annualRateBp,
      durationDays: investments.durationDays,
      daysPaid: investments.daysPaid,
      earnedUsd: investments.earnedUsd,
      status: investments.status,
      startedAt: investments.startedAt,
      endsAt: investments.endsAt,
      planName: plans.name,
      planSlug: plans.slug,
      riskLevel: plans.riskLevel,
    })
    .from(investments)
    .innerJoin(plans, eq(plans.id, investments.planId))
    .where(eq(investments.userId, userId))
    .orderBy(desc(investments.startedAt));
}

export type PlanSummaryRow = {
  id: number;
  slug: string;
  name: string;
  tagline: string;
  riskLevel: string;
  minUsd: string;
  maxUsd: string;
  annualRateBp: number;
  durationDays: number;
  subscribers: number;
  simulatedPrincipal: string;
};

export async function plansWithStats(): Promise<PlanSummaryRow[]> {
  // Two plain queries merged in memory rather than correlated sub-selects: the
  // in-memory demo database does not evaluate those reliably, and this version
  // is just as cheap for a catalogue of a few plans.
  const [catalogue, stats] = await Promise.all([
    db
      .select({
        id: plans.id,
        slug: plans.slug,
        name: plans.name,
        tagline: plans.tagline,
        riskLevel: plans.riskLevel,
        minUsd: plans.minAmountUsd,
        maxUsd: plans.maxAmountUsd,
        annualRateBp: plans.annualRateBp,
        durationDays: plans.durationDays,
      })
      .from(plans)
      .where(eq(plans.status, "active"))
      .orderBy(asc(plans.sortOrder), asc(plans.id)),
    db
      .select({
        planId: investments.planId,
        subscribers: sql<string>`count(*)`,
        principal: sql<string>`coalesce(sum(${investments.principalUsd}), 0)::text`,
      })
      .from(investments)
      .groupBy(investments.planId),
  ]);

  const byPlan = new Map(stats.map((s) => [s.planId, s]));

  return catalogue.map((plan) => {
    const stat = byPlan.get(plan.id);
    return {
      ...plan,
      subscribers: Number(stat?.subscribers ?? 0),
      simulatedPrincipal: centsToUsd(toCents(stat?.principal ?? "0")),
    };
  });
}

export async function planStats() {
  const [row] = await db
    .select({
      active: sql<number>`count(*) filter (where status = 'active')`,
      total: sql<number>`count(*)`,
      principal: sql<string>`coalesce(sum(principal_usd) filter (where status = 'active'), 0)::text`,
      earned: sql<string>`coalesce(sum(earned_usd), 0)::text`,
    })
    .from(investments);
  return {
    active: Number(row?.active ?? 0),
    total: Number(row?.total ?? 0),
    principal: centsToNumber(toCents(row?.principal ?? "0")),
    earned: centsToNumber(toCents(row?.earned ?? "0")),
  };
}

export async function newUsersThisWeek(): Promise<number> {
  const [row] = await db
    .select({ n: sql<number>`count(*)` })
    .from(users)
    .where(sql`${users.createdAt} > now() - interval '7 days'`);
  return Number(row?.n ?? 0);
}
