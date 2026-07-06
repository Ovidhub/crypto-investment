import { db } from "@/db";
import { users, holdings, transactions } from "@/db/schema";
import { hashPassword, setSession } from "@/lib/auth";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export async function POST(req: Request) {
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { name, email, password } = (body ?? {}) as Record<string, unknown>;

  if (typeof name !== "string" || name.trim().length < 2) {
    return Response.json({ error: "Please enter your name." }, { status: 400 });
  }
  if (typeof email !== "string" || !EMAIL_RE.test(email)) {
    return Response.json({ error: "Enter a valid email." }, { status: 400 });
  }
  if (typeof password !== "string" || password.length < 6) {
    return Response.json(
      { error: "Password must be at least 6 characters." },
      { status: 400 },
    );
  }

  const normEmail = email.trim().toLowerCase();

  try {
    const existing = await db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.email, normEmail))
      .limit(1);
    if (existing.length) {
      return Response.json(
        { error: "An account with this email already exists." },
        { status: 409 },
      );
    }

    const [user] = await db
      .insert(users)
      .values({
        name: name.trim().slice(0, 120),
        email: normEmail,
        passwordHash: hashPassword(password),
        balanceUsd: "10000",
      })
      .returning({ id: users.id });

    // Give a small starter portfolio so the dashboard has data.
    await db.insert(holdings).values([
      { userId: user.id, coinId: "bitcoin", symbol: "BTC", amount: "0.05" },
      { userId: user.id, coinId: "ethereum", symbol: "ETH", amount: "1.2" },
    ]);
    await db.insert(transactions).values({
      userId: user.id,
      type: "deposit",
      usdValue: "10000",
    });

    await setSession(user.id);
    return Response.json({ ok: true });
  } catch {
    return Response.json({ error: "Registration failed." }, { status: 500 });
  }
}
