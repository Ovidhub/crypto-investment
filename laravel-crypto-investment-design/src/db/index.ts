import { drizzle, type NodePgDatabase } from "drizzle-orm/node-postgres";
import { Pool, type PoolClient } from "pg";
import { createDemoDatabase, type DemoUser } from "./demo";
import { runBootstrap } from "./bootstrap";

/**
 * Postgres connection string.
 *
 * Vercel's storage integrations inject different names depending on the
 * provider (Neon uses DATABASE_URL, Supabase uses POSTGRES_URL, and both also
 * export the PG* components). Accept any of them so "connect a database in the
 * dashboard" is genuinely all that is required.
 */
export function resolveDatabaseUrl(): string | undefined {
  const candidates = [
    process.env.DATABASE_URL,
    process.env.POSTGRES_URL,
    process.env.POSTGRES_PRISMA_URL,
    process.env.DATABASE_URL_UNPOOLED,
    process.env.POSTGRES_URL_NON_POOLING,
  ];
  for (const candidate of candidates) {
    if (candidate && candidate.trim()) return candidate.trim();
  }
  const { PGHOST, PGUSER, PGDATABASE } = process.env;
  if (PGHOST && PGUSER && PGDATABASE) {
    const user = encodeURIComponent(PGUSER);
    const password = process.env.PGPASSWORD
      ? `:${encodeURIComponent(process.env.PGPASSWORD)}`
      : "";
    const port = process.env.PGPORT ?? "5432";
    return `postgresql://${user}${password}@${PGHOST}:${port}/${PGDATABASE}?sslmode=require`;
  }
  return undefined;
}

const databaseUrl = resolveDatabaseUrl();

/** True when no database is configured, so the in-memory engine is used. */
export const isDemoMode = !databaseUrl;

type DatabaseInstance = {
  pool: Pool;
  ensureUser?: (user: DemoUser) => Promise<void>;
  bootstrap?: Promise<void>;
};

const globalForDb = globalThis as typeof globalThis & {
  __enethiDatabase?: DatabaseInstance;
};

/**
 * Wrap a real Postgres pool so the schema bootstrap runs exactly once before
 * the first query of the process. Both query() and connect() (used by
 * drizzle transactions) are covered, and the bootstrap itself talks to the
 * raw, unwrapped pool so it cannot recurse.
 */
function bootstrapPool(pool: Pool): Pool {
  const rawQuery = pool.query.bind(pool) as Pool["query"];
  const rawConnect = pool.connect.bind(pool);

  const run: Promise<void> = (async () => {
    try {
      await runBootstrap(async (text, values) => rawQuery(text, values as never));
    } catch (error) {
      // Never take the site down because of a bootstrap hiccup: log it and let
      // the request surface its own error if a table really is missing.
      console.error("Schema bootstrap failed", error);
    }
  })();

  pool.query = (async (...args: Parameters<Pool["query"]>) => {
    await run;
    return (rawQuery as (...a: Parameters<Pool["query"]>) => unknown)(...args);
  }) as Pool["query"];

  pool.connect = (async () => {
    await run;
    const client = (await rawConnect()) as PoolClient;
    return client;
  }) as Pool["connect"];

  return pool;
}

function createInstance(): DatabaseInstance {
  if (databaseUrl) {
    const needsSsl = /sslmode=require|neon\.tech|supabase|prisma\.io|vercel-storage/i.test(
      databaseUrl,
    );
    const pool = bootstrapPool(
      new Pool({
        connectionString: databaseUrl,
        ssl: needsSsl ? { rejectUnauthorized: false } : undefined,
        max: 5,
        idleTimeoutMillis: 10_000,
        connectionTimeoutMillis: 10_000,
      }),
    );
    return { pool };
  }
  return createDemoDatabase();
}

const instance = (globalForDb.__enethiDatabase ??= createInstance());

export const pool = instance.pool;
export const db: NodePgDatabase = drizzle(pool);

/**
 * In demo mode each serverless function has its own in-memory database.
 * Re-create a signed-in user (and their starter portfolio) in this instance
 * before pages query it, so authentication survives cold starts.
 */
export async function ensureDemoUser(user: DemoUser): Promise<void> {
  if (!isDemoMode || !instance.ensureUser) return;
  await instance.ensureUser(user);
}
