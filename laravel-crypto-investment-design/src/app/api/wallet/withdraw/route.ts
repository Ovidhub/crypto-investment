import { apiUser } from "@/lib/auth";
import { requestWithdrawal } from "@/lib/wallet";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(
    clientKey(req, `withdraw:${auth.user.id}`),
    10,
    60 * 60 * 1000,
  );
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { amount, destination } = (body ?? {}) as Record<string, unknown>;
  if (typeof amount !== "string" && typeof amount !== "number") {
    return Response.json({ error: "Enter an amount." }, { status: 400 });
  }
  if (typeof destination !== "string") {
    return Response.json({ error: "Enter a destination." }, { status: 400 });
  }

  const result = await requestWithdrawal(auth.user.id, String(amount), destination);
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status ?? 400 });
  }
  return Response.json({ ok: true, message: result.message, id: result.id });
}
