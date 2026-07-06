import { db } from "@/db";
import { users, holdings, transactions } from "@/db/schema";
import { getCurrentUser } from "@/lib/auth";
import { fetchLiveCoins } from "@/lib/coins";
import { and, eq } from "drizzle-orm";

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
  const { type, coinId, usdAmount } = (body ?? {}) as Record<string, unknown>;

  if (type !== "buy" && type !== "sell") {
    return Response.json({ error: "Invalid trade type." }, { status: 400 });
  }
  if (typeof coinId !== "string") {
    return Response.json({ error: "Coin required." }, { status: 400 });
  }
  const usd = Number(usdAmount);
  if (!Number.isFinite(usd) || usd <= 0) {
    return Response.json({ error: "Enter a valid amount." }, { status: 400 });
  }

  const coins = await fetchLiveCoins();
  const coin = coins.find((c) => c.id === coinId);
  if (!coin) return Response.json({ error: "Unknown coin." }, { status: 400 });

  const coinAmount = usd / coin.price;
  const balance = Number(me.balanceUsd);

  try {
    if (type === "buy") {
      if (usd > balance) {
        return Response.json(
          { error: "Insufficient cash balance." },
          { status: 400 },
        );
      }
      await db
        .update(users)
        .set({ balanceUsd: (balance - usd).toFixed(2) })
        .where(eq(users.id, me.id));

      const [existing] = await db
        .select()
        .from(holdings)
        .where(and(eq(holdings.userId, me.id), eq(holdings.coinId, coinId)))
        .limit(1);

      if (existing) {
        await db
          .update(holdings)
          .set({ amount: (Number(existing.amount) + coinAmount).toFixed(10) })
          .where(eq(holdings.id, existing.id));
      } else {
        await db.insert(holdings).values({
          userId: me.id,
          coinId,
          symbol: coin.symbol,
          amount: coinAmount.toFixed(10),
        });
      }
    } else {
      // sell
      const [existing] = await db
        .select()
        .from(holdings)
        .where(and(eq(holdings.userId, me.id), eq(holdings.coinId, coinId)))
        .limit(1);
      const held = existing ? Number(existing.amount) : 0;
      if (coinAmount > held) {
        return Response.json(
          { error: `You only hold ${held.toFixed(6)} ${coin.symbol}.` },
          { status: 400 },
        );
      }
      await db
        .update(holdings)
        .set({ amount: (held - coinAmount).toFixed(10) })
        .where(eq(holdings.id, existing!.id));
      await db
        .update(users)
        .set({ balanceUsd: (balance + usd).toFixed(2) })
        .where(eq(users.id, me.id));
    }

    await db.insert(transactions).values({
      userId: me.id,
      type,
      coinId,
      symbol: coin.symbol,
      amount: coinAmount.toFixed(10),
      price: coin.price.toFixed(6),
      usdValue: usd.toFixed(2),
    });

    return Response.json({ ok: true });
  } catch {
    return Response.json({ error: "Trade failed." }, { status: 500 });
  }
}
