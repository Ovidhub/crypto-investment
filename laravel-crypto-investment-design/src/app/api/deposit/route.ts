import { db } from "@/db";
import { users, transactions } from "@/db/schema";
import { getCurrentUser } from "@/lib/auth";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const me = await getCurrentUser();
  if (!me) return Response.json({ error: "Not authenticated." }, { status: 401 });

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { type, usdAmount } = (body ?? {}) as Record<string, unknown>;
  const kind = type === "withdraw" ? "withdraw" : "deposit";
  const usd = Number(usdAmount);
  if (!Number.isFinite(usd) || usd <= 0) {
    return Response.json({ error: "Enter a valid amount." }, { status: 400 });
  }

  const balance = Number(me.balanceUsd);
  if (kind === "withdraw" && usd > balance) {
    return Response.json({ error: "Insufficient balance." }, { status: 400 });
  }

  try {
    const next = kind === "deposit" ? balance + usd : balance - usd;
    await db
      .update(users)
      .set({ balanceUsd: next.toFixed(2) })
      .where(eq(users.id, me.id));
    await db.insert(transactions).values({
      userId: me.id,
      type: kind,
      usdValue: usd.toFixed(2),
    });
    return Response.json({ ok: true, balance: next.toFixed(2) });
  } catch {
    return Response.json({ error: "Operation failed." }, { status: 500 });
  }
}
