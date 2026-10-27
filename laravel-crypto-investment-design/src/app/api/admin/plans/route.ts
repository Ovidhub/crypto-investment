import { eq } from "drizzle-orm";
import { db } from "@/db";
import { plans } from "@/db/schema";
import { apiAdmin } from "@/lib/auth";
import { logAudit } from "@/lib/audit";
import { centsToUsd, parseUsdInput, toCents } from "@/lib/money";

export const dynamic = "force-dynamic";

/**
 * Plan catalogue management.
 *
 * Admins can shape products (price bands, duration, simulated annual rate) but
 * this endpoint deliberately exposes no "credit a user's balance" action: money
 * only enters an account through an approved deposit request, plan accrual or a
 * trade.
 */
export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { action, slug, name, tagline, minAmount, maxAmount, annualRateBp, durationDays, status } =
    (body ?? {}) as Record<string, unknown>;

  if (action === "toggle") {
    if (typeof slug !== "string") {
      return Response.json({ error: "Plan required." }, { status: 400 });
    }
    const [plan] = await db.select().from(plans).where(eq(plans.slug, slug)).limit(1);
    if (!plan) return Response.json({ error: "Plan not found." }, { status: 404 });
    const next = plan.status === "active" ? "paused" : "active";
    if (status === "active" || status === "paused") {
      // explicit status wins
    }
    await db
      .update(plans)
      .set({ status: status === "active" || status === "paused" ? status : next })
      .where(eq(plans.slug, slug));
    await logAudit({
      actorId: auth.user.id,
      actorEmail: auth.user.email,
      action: "plan.toggle",
      targetType: "plan",
      targetId: plan.id,
    });
    return Response.json({ ok: true });
  }

  // Create or update
  const min = typeof minAmount === "string" ? parseUsdInput(minAmount) : null;
  const max = typeof maxAmount === "string" ? parseUsdInput(maxAmount) : null;
  const bp = Number(annualRateBp);
  const days = Number(durationDays);

  if (typeof slug !== "string" || !/^[a-z0-9-]{3,40}$/.test(slug)) {
    return Response.json(
      { error: "Slug must be 3-40 lowercase letters, digits or dashes." },
      { status: 400 },
    );
  }
  if (typeof name !== "string" || name.trim().length < 2) {
    return Response.json({ error: "Plan name required." }, { status: 400 });
  }
  if (!min || !max || min <= BigInt(0) || max < min) {
    return Response.json({ error: "Check the min/max amounts." }, { status: 400 });
  }
  if (!Number.isInteger(bp) || bp < 0 || bp > 10_000) {
    return Response.json(
      { error: "Annual rate must be between 0% and 100%." },
      { status: 400 },
    );
  }
  if (!Number.isInteger(days) || days < 1 || days > 1095) {
    return Response.json({ error: "Duration must be 1-1095 days." }, { status: 400 });
  }

  const values = {
    slug,
    name: name.trim().slice(0, 80),
    tagline: (typeof tagline === "string" ? tagline.trim() : "").slice(0, 200) || "Simulated plan",
    riskLevel: "balanced",
    minAmountUsd: centsToUsd(min),
    maxAmountUsd: centsToUsd(max),
    annualRateBp: bp,
    durationDays: days,
    status: typeof status === "string" && status === "paused" ? "paused" : "active",
  };

  const [existing] = await db.select({ id: plans.id }).from(plans).where(eq(plans.slug, slug)).limit(1);
  if (existing) {
    await db.update(plans).set(values).where(eq(plans.slug, slug));
  } else {
    await db.insert(plans).values(values);
  }

  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: existing ? "plan.update" : "plan.create",
    targetType: "plan",
    meta: { slug, annualRateBp: bp, durationDays: days },
  });

  return Response.json({ ok: true });
}

export async function GET() {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;
  const rows = await db.select().from(plans);
  return Response.json({
    plans: rows.map((p) => ({
      ...p,
      minUsd: centsToUsd(toCents(p.minAmountUsd)),
      maxUsd: centsToUsd(toCents(p.maxAmountUsd)),
    })),
  });
}
