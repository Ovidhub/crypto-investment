import { db } from "@/db";
import { users } from "@/db/schema";
import { verifyPassword, setSession } from "@/lib/auth";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
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

  try {
    const [user] = await db
      .select()
      .from(users)
      .where(eq(users.email, email.trim().toLowerCase()))
      .limit(1);

    if (!user || !verifyPassword(password, user.passwordHash)) {
      return Response.json(
        { error: "Invalid email or password." },
        { status: 401 },
      );
    }
    if (user.status !== "active") {
      return Response.json(
        { error: "Your account has been suspended." },
        { status: 403 },
      );
    }

    await setSession(user.id);
    return Response.json({ ok: true, role: user.role });
  } catch {
    return Response.json({ error: "Login failed." }, { status: 500 });
  }
}
