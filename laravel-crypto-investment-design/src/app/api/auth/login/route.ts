import { db } from "@/db";
import { users } from "@/db/schema";
import { verifyPassword, setSession } from "@/lib/auth";
import { logAudit } from "@/lib/audit";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const limited = rateLimit(clientKey(req, "login"), 8, 5 * 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { email, password } = (body ?? {}) as Record<string, unknown>;

  if (typeof email !== "string" || typeof password !== "string") {
    return Response.json({ error: "Email and password required." }, { status: 400 });
  }

  const normEmail = email.trim().toLowerCase();

  try {
    const [user] = await db
      .select()
      .from(users)
      .where(eq(users.email, normEmail))
      .limit(1);

    if (!user || !verifyPassword(password, user.passwordHash)) {
      await logAudit({
        actorEmail: normEmail.slice(0, 200),
        action: "auth.login.failed",
      });
      return Response.json(
        { error: "Invalid email or password." },
        { status: 401 },
      );
    }
    if (user.status !== "active") {
      return Response.json(
        { error: "This account is suspended. Contact support." },
        { status: 403 },
      );
    }

    await setSession(user);
    await logAudit({
      actorId: user.id,
      actorEmail: user.email,
      action: "auth.login",
    });
    return Response.json({ ok: true, role: user.role });
  } catch {
    return Response.json({ error: "Login failed." }, { status: 500 });
  }
}
