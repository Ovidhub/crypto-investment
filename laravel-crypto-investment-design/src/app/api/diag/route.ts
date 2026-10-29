import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/** TEMPORARY: connection diagnostics that bypass the shared pool/bootstrap. */
export async function GET() {
  const names = [
    "DATABASE_URL",
    "POSTGRES_URL",
    "POSTGRES_PRISMA_URL",
    "DATABASE_URL_UNPOOLED",
    "POSTGRES_URL_NON_POOLING",
    "PGHOST",
    "PGUSER",
    "PGDATABASE",
  ];
  const present: Record<string, boolean> = {};
  for (const name of names) present[name] = Boolean(process.env[name]);

  const raw =
    process.env.DATABASE_URL ??
    process.env.POSTGRES_URL ??
    process.env.POSTGRES_PRISMA_URL ??
    "";

  let host = "(none)";
  try {
    const parsed = new URL(raw);
    host = `${parsed.hostname}${parsed.port ? `:${parsed.port}` : ""}`;
  } catch {
    host = raw ? "(unparseable)" : "(none)";
  }

  const started = Date.now();

  // Independent probe: does the app's own pooled connection answer?
  const app: Record<string, unknown> = {};
  try {
    const dbModule = await Promise.race([
      import("@/db"),
      new Promise((resolve) => setTimeout(() => resolve(null), 5_000)),
    ]);
    if (!dbModule) {
      app.imported = false;
      app.note = "module import timed out";
    } else {
      const mod = dbModule as typeof import("@/db");
      app.imported = true;
      app.demoMode = mod.isDemoMode;
      const t0 = Date.now();
      try {
        const row = await Promise.race([
          mod.pool.query("select 1 as ok"),
          new Promise((_, reject) =>
            setTimeout(() => reject(new Error("pool query exceeded 20s")), 20_000),
          ),
        ]);
        app.poolQueryMs = Date.now() - t0;
        app.poolOk = Array.isArray((row as { rows?: unknown[] }).rows);
      } catch (error) {
        app.poolQueryMs = Date.now() - t0;
        app.poolError = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
      }
    }
  } catch (error) {
    app.imported = false;
    app.error = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
  }

  let result: unknown;
  try {
    const { Client } = await import("pg");
    const client = new Client({
      connectionString: raw,
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 6_000,
      query_timeout: 8_000,
    });
    await client.connect();
    const rows = await client.query("select now() as now, current_database() as db");
    await client.end();
    result = { connected: true, db: rows.rows[0] };
  } catch (error) {
    result = {
      connected: false,
      error: error instanceof Error ? `${error.name}: ${error.message}` : String(error),
    };
  }

  return NextResponse.json({
    app,
    host,
    present,
    result,
    ms: Date.now() - started,
    phase: process.env.NEXT_PHASE ?? null,
    vercelEnv: process.env.VERCEL_ENV ?? null,
  });
}
