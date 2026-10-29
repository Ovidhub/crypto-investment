import { NextResponse } from "next/server";
import { db, isDemoMode, pool } from "@/db";
import { sql } from "drizzle-orm";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

function cap<T>(promise: Promise<T>, ms: number, label: string) {
  return Promise.race([
    promise,
    new Promise<never>((_, reject) =>
      setTimeout(() => reject(new Error(`${label} exceeded ${ms}ms`)), ms),
    ),
  ]);
}

/** TEMPORARY: verifies the pooled app layer now answers. */
export async function GET() {
  const out: Record<string, unknown> = { isDemoMode };
  const t0 = Date.now();
  try {
    const ping = await cap(pool.query("select 1 as ok"), 15_000, "pool.query");
    out.poolMs = Date.now() - t0;
    out.poolOk = Array.isArray(ping.rows);
  } catch (error) {
    out.poolMs = Date.now() - t0;
    out.poolError = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
  }
  const t1 = Date.now();
  try {
    const rows = await cap(
      db.execute(sql`select
        (select count(*) from users)::int as users,
        (select count(*) from site_content)::int as content_rows,
        (select count(*) from plans)::int as plans,
        (select count(*) from ledger_accounts)::int as ledger_accounts`),
      15_000,
      "db.execute",
    );
    out.dbMs = Date.now() - t1;
    out.counts = (rows as unknown as { rows?: unknown[] }).rows;
  } catch (error) {
    out.dbMs = Date.now() - t1;
    out.dbError = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
  }
  return NextResponse.json(out);
}
