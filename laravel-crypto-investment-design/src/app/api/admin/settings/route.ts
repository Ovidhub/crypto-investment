import { apiAdmin } from "@/lib/auth";
import { logAudit } from "@/lib/audit";
import { allFlags, setFlag } from "@/lib/wallet";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;
  return Response.json({ flags: await allFlags() });
}

export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { key, enabled } = (body ?? {}) as Record<string, unknown>;
  if (typeof key !== "string" || typeof enabled !== "boolean") {
    return Response.json({ error: "Flag key and value required." }, { status: 400 });
  }
  const allowed = ["plans", "deposits", "withdrawals", "trading", "referrals", "kyc"];
  if (!allowed.includes(key)) {
    return Response.json({ error: "Unknown flag." }, { status: 400 });
  }

  await setFlag(key, enabled);
  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: "settings.flag",
    targetType: "feature_flag",
    meta: { key, enabled },
  });
  return Response.json({ ok: true });
}
