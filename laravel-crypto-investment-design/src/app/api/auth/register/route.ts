import { db } from "@/db";
import { users } from "@/db/schema";
import { eq } from "drizzle-orm";
import { setSession } from "@/lib/auth";
import { registerUser } from "@/lib/accounts";
import { rateLimit, clientKey } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export async function POST(req: Request) {
  const limited = rateLimit(clientKey(req, "register"), 10, 60 * 60 * 1000);
  if (!limited.ok) {
    return Response.json(
      { error: "Too many sign-up attempts. Try again later." },
      { status: 429 },
    );
  }

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { name, email, password, referral } = (body ?? {}) as Record<string, unknown>;

  if (typeof name !== "string" || name.trim().length < 2) {
    return Response.json({ error: "Please enter your name." }, { status: 400 });
  }
  if (typeof email !== "string" || !EMAIL_RE.test(email)) {
    return Response.json({ error: "Enter a valid email." }, { status: 400 });
  }
  if (typeof password !== "string" || password.length < 10) {
    return Response.json(
      { error: "Password must be at least 10 characters." },
      { status: 400 },
    );
  }

  try {
    const result = await registerUser({
      name,
      email,
      password,
      referral: typeof referral === "string" ? referral : null,
    });
    if (!result.ok) {
      return Response.json({ error: result.error }, { status: result.status });
    }

    const [user] = await db
      .select()
      .from(users)
      .where(eq(users.id, result.userId))
      .limit(1);
    if (user) await setSession(user);

    return Response.json({ ok: true });
  } catch {
    return Response.json({ error: "Registration failed." }, { status: 500 });
  }
}
