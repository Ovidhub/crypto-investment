import { eq } from "drizzle-orm";
import { db, isDemoMode } from "@/db";
import { users } from "@/db/schema";
import { logAudit } from "@/lib/audit";
import { setSession } from "@/lib/auth";
import { DEMO_ACCOUNT } from "@/lib/demo-access";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

/**
 * One-click sign-in to the shared demo account.
 *
 * Only available while the app runs on the in-memory demo database — with a
 * real DATABASE_URL configured this endpoint refuses, because a public
 * passwordless door into a database-backed site would be a serious hole.
 */
export async function POST(req: Request) {
  if (!isDemoMode) {
    return Response.json(
      { error: "Demo sign-in is only available in demo mode." },
      { status: 404 },
    );
  }

  const limited = rateLimit(clientKey(req, "auth:demo"), 10, 5 * 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  const [user] = await db
    .select()
    .from(users)
    .where(eq(users.email, DEMO_ACCOUNT.email))
    .limit(1);

  if (!user) {
    return Response.json(
      { error: "The demo account is not seeded in this instance." },
      { status: 404 },
    );
  }
  if (user.status !== "active") {
    return Response.json({ error: "The demo account is suspended." }, { status: 403 });
  }

  await setSession(user);
  await logAudit({
    actorId: user.id,
    actorEmail: user.email,
    action: "auth.login.demo",
  });

  return Response.json({ ok: true, role: user.role });
}
