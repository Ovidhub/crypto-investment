import { apiUser } from "@/lib/auth";
import { changePassword } from "@/lib/accounts";
import { logAudit } from "@/lib/audit";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `pw:${auth.user.id}`), 5, 15 * 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { currentPassword, newPassword } = (body ?? {}) as Record<string, unknown>;
  if (typeof currentPassword !== "string" || typeof newPassword !== "string") {
    return Response.json({ error: "Both passwords are required." }, { status: 400 });
  }

  const result = await changePassword(auth.user.id, currentPassword, newPassword);
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: 400 });
  }
  await logAudit({
    actorId: auth.user.id,
    action: "account.password.change",
    targetType: "user",
    targetId: auth.user.id,
  });
  return Response.json({ ok: true, message: "Password updated." });
}
