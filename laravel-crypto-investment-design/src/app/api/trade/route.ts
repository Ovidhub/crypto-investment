import { apiUser } from "@/lib/auth";
import { executeTrade } from "@/lib/trading";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `trade:${auth.user.id}`), 30, 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { type, coinId, usdAmount } = (body ?? {}) as Record<string, unknown>;

  if (type !== "buy" && type !== "sell") {
    return Response.json({ error: "Invalid trade type." }, { status: 400 });
  }
  if (typeof coinId !== "string") {
    return Response.json({ error: "Coin required." }, { status: 400 });
  }

  const result = await executeTrade(
    auth.user.id,
    type,
    coinId,
    typeof usdAmount === "number" ? usdAmount.toFixed(2) : String(usdAmount ?? ""),
  );
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status ?? 400 });
  }
  return Response.json({
    ok: true,
    message: result.message,
    amount: result.amount,
    price: result.price,
    fee: result.fee,
  });
}
