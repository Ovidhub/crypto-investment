import { and, desc, eq, isNull, sql } from "drizzle-orm";
import { db } from "@/db";
import { notifications } from "@/db/schema";
import { apiUser } from "@/lib/auth";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const rows = await db
    .select()
    .from(notifications)
    .where(eq(notifications.userId, auth.user.id))
    .orderBy(desc(notifications.createdAt))
    .limit(50);

  const [unread] = await db
    .select({ n: sql<number>`count(*)` })
    .from(notifications)
    .where(and(eq(notifications.userId, auth.user.id), isNull(notifications.readAt)));

  return Response.json({ notifications: rows, unread: Number(unread?.n ?? 0) });
}

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  let body: unknown = {};
  try {
    body = await req.json();
  } catch {
    body = {};
  }
  const { id } = (body ?? {}) as Record<string, unknown>;

  if (Number.isInteger(Number(id))) {
    await db
      .update(notifications)
      .set({ readAt: new Date() })
      .where(
        and(
          eq(notifications.id, Number(id)),
          eq(notifications.userId, auth.user.id),
        ),
      );
    return Response.json({ ok: true });
  }

  await db
    .update(notifications)
    .set({ readAt: new Date() })
    .where(and(eq(notifications.userId, auth.user.id), isNull(notifications.readAt)));
  return Response.json({ ok: true });
}
