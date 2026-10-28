import { apiAdmin } from "@/lib/auth";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";
import { listDeskTrades, listSettledTrades, settleSimTrade } from "@/lib/sim-trades";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  return Response.json({
    open: await listDeskTrades(),
    settled: await listSettledTrades(20),
  });
}

export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `settle:${auth.user.id}`), 60, 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { tradeId, outcome, reason } = (body ?? {}) as Record<string, unknown>;

  const id = Number(tradeId);
  if (!Number.isInteger(id) || id <= 0) {
    return Response.json({ error: "Trade required." }, { status: 400 });
  }
  if (typeof outcome !== "string") {
    return Response.json({ error: "Choose an outcome." }, { status: 400 });
  }

  const result = await settleSimTrade({
    admin: { id: auth.user.id, email: auth.user.email },
    tradeId: id,
    outcome: outcome as "won" | "lost" | "void" | "market",
    reason: typeof reason === "string" ? reason : "",
  });

  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status });
  }
  return Response.json(result);
}
