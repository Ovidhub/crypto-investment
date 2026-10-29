import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 30;

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
    host,
    present,
    result,
    ms: Date.now() - started,
    phase: process.env.NEXT_PHASE ?? null,
    vercelEnv: process.env.VERCEL_ENV ?? null,
  });
}
