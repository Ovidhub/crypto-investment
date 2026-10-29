import { apiAdmin } from "@/lib/auth";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";
import {
  applyPolicyNow,
  isOutcomePolicy,
  POLICY_LABELS,
  setOutcomePolicy,
} from "@/lib/sim-trades";

export const dynamic = "force-dynamic";

/**
 * Choose how an account's simulated trades resolve.
 *
 * body: { userId, policy: "manual"|"win"|"loss"|"market", reason?, applyNow? }
 *
 * `applyNow` also settles the account's currently open trades under the new
 * policy. Without it the policy only affects trades opened from now on.
 */
export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `policy:${auth.user.id}`), 60, 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }

  const { userId, policy, reason, applyNow } = (body ?? {}) as Record<string, unknown>;
  const id = Number(userId);
  if (!Number.isInteger(id) || id <= 0) {
    return Response.json({ error: "Choose an account." }, { status: 400 });
  }
  if (!isOutcomePolicy(policy)) {
    return Response.json({ error: "Choose a valid outcome policy." }, { status: 400 });
  }

  const note = typeof reason === "string" ? reason : "";
  const admin = { id: auth.user.id, email: auth.user.email };

  const saved = await setOutcomePolicy({ admin, userId: id, policy, reason: note });
  if (!saved.ok) {
    return Response.json({ error: saved.error }, { status: saved.status });
  }

  let settled = 0;
  const failures: string[] = [];
  if (applyNow === true && policy !== "manual") {
    const applied = await applyPolicyNow({
      actor: admin,
      userId: id,
      policy,
      reason: note.trim() || `New house policy: ${POLICY_LABELS[policy].toLowerCase()}.`,
    });
    settled = applied.settled;
    failures.push(...applied.failures);
  }

  const tail =
    settled > 0
      ? ` ${settled} open trade${settled === 1 ? "" : "s"} settled now.`
      : "";

  return Response.json({
    ok: true,
    policy,
    settledNow: settled,
    failures,
    message: `${saved.message}${tail}`,
  });
}
