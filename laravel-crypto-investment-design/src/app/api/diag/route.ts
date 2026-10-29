import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 30;

function cap<T>(promise: Promise<T>, ms: number, label: string) {
  return Promise.race([
    promise,
    new Promise<never>((_, reject) =>
      setTimeout(() => reject(new Error(`${label} exceeded ${ms}ms`)), ms),
    ),
  ]);
}

/** TEMPORARY: does importing the app's db module work in this runtime? */
export async function GET() {
  const out: Record<string, unknown> = {};
  try {
    const mod = await cap(import("@/db"), 4_000, "import");
    out.imported = true;
    out.isDemoMode = mod.isDemoMode;
    const t0 = Date.now();
    try {
      const result = await cap(mod.pool.query("select 1 as ok"), 5_000, "pool.query");
      out.poolMs = Date.now() - t0;
      out.poolRows = (result as { rows?: unknown[] }).rows;
    } catch (error) {
      out.poolMs = Date.now() - t0;
      out.poolError = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
    }
    const t1 = Date.now();
    try {
      const { sql } = await import("drizzle-orm");
      const result = await cap(mod.db.execute(sql`select 1 as ok`), 5_000, "db.execute");
      out.dbMs = Date.now() - t1;
      out.dbOk = Boolean(result);
    } catch (error) {
      out.dbMs = Date.now() - t1;
      out.dbError = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
    }
  } catch (error) {
    out.imported = false;
    out.importError =
      error instanceof Error ? `${error.name}: ${error.message}` : String(error);
  }
  return NextResponse.json(out);
}
