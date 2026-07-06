import { db } from "@/db";
import { users } from "@/db/schema";
import { getCurrentUser } from "@/lib/auth";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";

export async function PATCH(req: Request) {
  const me = await getCurrentUser();
  if (!me || me.role !== "admin") {
    return Response.json({ error: "Forbidden." }, { status: 403 });
  }

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { userId, action } = (body ?? {}) as Record<string, unknown>;
  const id = Number(userId);
  if (!Number.isInteger(id)) {
    return Response.json({ error: "Invalid user." }, { status: 400 });
  }
  if (id === me.id) {
    return Response.json(
      { error: "You cannot modify your own account." },
      { status: 400 },
    );
  }

  try {
    if (action === "suspend") {
      await db
        .update(users)
        .set({ status: "suspended" })
        .where(eq(users.id, id));
    } else if (action === "activate") {
      await db.update(users).set({ status: "active" }).where(eq(users.id, id));
    } else if (action === "makeAdmin") {
      await db.update(users).set({ role: "admin" }).where(eq(users.id, id));
    } else if (action === "makeUser") {
      await db.update(users).set({ role: "user" }).where(eq(users.id, id));
    } else {
      return Response.json({ error: "Unknown action." }, { status: 400 });
    }
    return Response.json({ ok: true });
  } catch {
    return Response.json({ error: "Update failed." }, { status: 500 });
  }
}
