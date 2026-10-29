import { apiUser } from "@/lib/auth";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";
import { openSimTrade } from "@/lib/sim-trades";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `simtrade:${auth.user.id}`), 12, 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { coinId, direction, stake, durationMinutes } = (body ?? {}) as Record<string, unknown>;

  if (typeof coinId !== "string") {
    return Response.json({ error: "Pick a market." }, { status: 400 });
  }

  const result = await openSimTrade({
    userId: auth.user.id,
    coinId,
    direction: typeof direction === "string" ? direction : "",
    rawStake: typeof stake === "string" ? stake : String(stake ?? ""),
    durationMinutes: Number(durationMinutes),
  });

  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status });
  }
  return Response.json(result);
}
