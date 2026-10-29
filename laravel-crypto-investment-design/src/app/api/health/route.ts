import { db, isDemoMode } from "@/db";
import { sql } from "drizzle-orm";

export const dynamic = "force-dynamic";

export async function GET() {
  const adminConfigured = Boolean(
    process.env.ADMIN_EMAIL && process.env.ADMIN_PASSWORD_HASH,
  );
  try {
    await db.execute(sql`select 1`);
    return Response.json({
      ok: true,
      storage: isDemoMode ? "in-memory" : "postgres",
      adminConfigured,
      sessionSecretConfigured: Boolean(process.env.AUTH_SECRET),
    });
  } catch {
    return Response.json(
      { ok: false, storage: isDemoMode ? "in-memory" : "postgres", adminConfigured },
      { status: 500 },
    );
  }
}
