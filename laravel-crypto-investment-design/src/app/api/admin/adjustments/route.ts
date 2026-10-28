import { apiAdmin } from "@/lib/auth";
import { adjustWallet, listAdjustments } from "@/lib/adjustments";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;
  return Response.json({ adjustments: await listAdjustments(100) });
}

export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `adjust:${auth.user.id}`), 20, 60 * 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { userId, direction, amount, reason } = (body ?? {}) as Record<string, unknown>;

  const id = Number(userId);
  if (!Number.isInteger(id)) {
    return Response.json({ error: "Choose a user." }, { status: 400 });
  }
  if (direction !== "credit" && direction !== "debit") {
    return Response.json({ error: "Direction must be credit or debit." }, { status: 400 });
  }
  if (typeof amount !== "string" && typeof amount !== "number") {
    return Response.json({ error: "Enter an amount." }, { status: 400 });
  }
  if (typeof reason !== "string") {
    return Response.json({ error: "A reason is required." }, { status: 400 });
  }

  const result = await adjustWallet({
    admin: { id: auth.user.id, email: auth.user.email },
    userId: id,
    direction,
    rawAmount: String(amount),
    reason,
  });

  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status });
  }
  return Response.json({
    ok: true,
    message: result.message,
    cashAfter: result.cashAfter,
  });
}
