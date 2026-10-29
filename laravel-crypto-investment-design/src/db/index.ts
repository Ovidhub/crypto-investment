import { drizzle, type NodePgDatabase } from "drizzle-orm/node-postgres";
import { Pool, type PoolClient } from "pg";
import { createDemoDatabase, type DemoUser } from "./demo";
import { runBootstrap } from "./bootstrap";

const databaseUrl = process.env.DATABASE_URL;

/** True when the app is running against the in-memory demo database. */
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
