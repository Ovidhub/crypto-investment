import { accrueAll } from "@/lib/plans";
import { logAudit } from "@/lib/audit";

export const dynamic = "force-dynamic";
export const maxDuration = 60;

/**
 * Simulated-yield accrual job. Run once a day (Vercel cron or any scheduler).
 * Protected by CRON_SECRET when it is configured:
 *   curl -H "Authorization: Bearer $CRON_SECRET" /api/cron/accrue
 */
export async function GET(req: Request) {
  const secret = process.env.CRON_SECRET;
  if (secret) {
    const header = req.headers.get("authorization") ?? "";
    if (header !== `Bearer ${secret}`) {
      return Response.json({ error: "Unauthorized." }, { status: 401 });
    }
  }

  try {
    const results = await accrueAll();
    const credited = results.filter((r) => r.credited !== "0.00");
    const total = credited.reduce((sum, r) => sum + Number(r.credited), 0);
    return Response.json({
      ok: true,
      ranAt: new Date().toISOString(),
      investments: results.length,
      credited: credited.length,
      totalCredited: total.toFixed(2),
      note: "Simulated accrual only. No real funds are involved.",
      results,
    });
  } catch (error) {
    await logAudit({
      action: "plan.accrual.error",
      meta: { message: error instanceof Error ? error.message : "unknown" },
    });
    return Response.json({ error: "Accrual run failed." }, { status: 500 });
  }
}
