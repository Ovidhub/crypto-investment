import { NextResponse } from "next/server";
import { BOOTSTRAP_DDL } from "@/db/bootstrap";

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

async function probe(fn: () => Promise<unknown>, ms: number, label: string) {
  const t0 = Date.now();
  try {
    const value = await cap(fn(), ms, label);
    return { ms: Date.now() - t0, ok: true, value };
  } catch (error) {
    return {
      ms: Date.now() - t0,
      ok: false,
      error: error instanceof Error ? `${error.name}: ${error.message}` : String(error),
    };
  }
}

/** TEMPORARY: narrow down where the pooled connection stalls. */
export async function GET() {
  const url = process.env.DATABASE_URL ?? process.env.POSTGRES_URL ?? "";
  const { Pool, Client } = await import("pg");
  const out: Record<string, unknown> = {};

  out.poolWithTimeouts = await probe(async () => {
    const pool = new Pool({
      connectionString: url,
      ssl: { rejectUnauthorized: false },
      max: 5,
      idleTimeoutMillis: 10_000,
      connectionTimeoutMillis: 10_000,
      statement_timeout: 20_000,
      query_timeout: 25_000,
    });
    const r = await pool.query("select 1 as ok");
    await pool.end();
    return r.rows;
  }, 8_000, "pool with timeouts");

  out.poolPlain = await probe(async () => {
    const pool = new Pool({
      connectionString: url,
      ssl: { rejectUnauthorized: false },
      max: 5,
      connectionTimeoutMillis: 10_000,
    });
    const r = await pool.query("select 1 as ok");
    await pool.end();
    return r.rows;
  }, 8_000, "plain pool");

  out.batchedDdl = await probe(async () => {
    const client = new Client({
      connectionString: url,
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 8_000,
      query_timeout: 6_000,
    });
    await client.connect();
    await client.query(BOOTSTRAP_DDL);
    await client.end();
    return "ddl ok";
  }, 9_000, "batched ddl");

  return NextResponse.json(out);
}
