import { and, eq } from "drizzle-orm";
import { db } from "@/db";
import { investments } from "@/db/schema";
import { apiUser } from "@/lib/auth";
import { completeInvestment } from "@/lib/plans";
import { logAudit } from "@/lib/audit";

export const dynamic = "force-dynamic";

/** Close an active simulation early. Principal is returned; accrual stops. */
export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { id } = (body ?? {}) as Record<string, unknown>;
  const investmentId = Number(id);
  if (!Number.isInteger(investmentId)) {
    return Response.json({ error: "Invalid investment." }, { status: 400 });
  }

  const [row] = await db
    .select({ id: investments.id })
    .from(investments)
    .where(and(eq(investments.id, investmentId), eq(investments.userId, auth.user.id)))
    .limit(1);
  if (!row) {
    return Response.json({ error: "Investment not found." }, { status: 404 });
  }

  const result = await completeInvestment(investmentId);
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status ?? 400 });
  }
  await logAudit({
    actorId: auth.user.id,
    action: "plan.close.early",
    targetType: "investment",
    targetId: investmentId,
  });
  return Response.json({ ok: true, message: result.message });
}
