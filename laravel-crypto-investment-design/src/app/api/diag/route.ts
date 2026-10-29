import { NextResponse } from "next/server";
import { BOOTSTRAP_DDL } from "@/db/bootstrap";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/** TEMPORARY: runs the schema bootstrap statement by statement, timed. */
export async function GET() {
  const raw =
    process.env.DATABASE_URL ??
    process.env.POSTGRES_URL ??
    process.env.POSTGRES_PRISMA_URL ??
    "";
  const out: Record<string, unknown> = {
    hasUrl: Boolean(raw),
    statements: [] as Array<Record<string, unknown>>,
  };

  const started = Date.now();
  try {
    const { Client } = await import("pg");
    const client = new Client({
      connectionString: raw,
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 6_000,
      query_timeout: 5_000,
    });
    await client.connect();
    out.connected = true;
    await client.query("SET statement_timeout = 4000");
    const existing = await client.query(
      `select table_name from information_schema.tables where table_schema = 'public' order by 1`,
    );
    out.tables = existing.rows.map((r: { table_name: string }) => r.table_name);

    const list = BOOTSTRAP_DDL.split(";")
      .map((s) => s.trim())
      .filter(Boolean);
    const statements = out.statements as Array<Record<string, unknown>>;
    for (const statement of list) {
      const t0 = Date.now();
      const head = statement.split("\n")[0].slice(0, 70);
      if (Date.now() - started > 20_000) {
        statements.push({ head, skipped: true });
        continue;
      }
      try {
        await client.query(statement);
        statements.push({ head, ms: Date.now() - t0 });
      } catch (error) {
        statements.push({
          head,
          ms: Date.now() - t0,
          error: error instanceof Error ? `${error.name}: ${error.message}` : String(error),
        });
      }
    }
    await client.end();
  } catch (error) {
    out.connected = false;
    out.error = error instanceof Error ? `${error.name}: ${error.message}` : String(error);
  }

  out.totalMs = Date.now() - started;
  return NextResponse.json(out);
}
