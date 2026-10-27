import { apiUser } from "@/lib/auth";
import { requestDeposit } from "@/lib/wallet";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `deposit:${auth.user.id}`), 10, 60 * 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { amount, method } = (body ?? {}) as Record<string, unknown>;
  if (typeof amount !== "string" && typeof amount !== "number") {
    return Response.json({ error: "Enter an amount." }, { status: 400 });
  }

  const result = await requestDeposit(
    auth.user.id,
    String(amount),
    typeof method === "string" ? method : "simulated",
  );
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status ?? 400 });
  }
  return Response.json({ ok: true, message: result.message, id: result.id });
}
