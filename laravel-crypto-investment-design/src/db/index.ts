import { drizzle, type NodePgDatabase } from "drizzle-orm/node-postgres";
import { Pool } from "pg";
import { createDemoDatabase, type DemoUser } from "./demo";

const databaseUrl = process.env.DATABASE_URL;

/** True when the app is running against the in-memory demo database. */
export const isDemoMode = !databaseUrl;

type DatabaseInstance = {
  pool: Pool;
  ensureUser?: (user: DemoUser) => Promise<void>;
};

const globalForDb = globalThis as typeof globalThis & {
  __enethiDatabase?: DatabaseInstance;
};

function createInstance(): DatabaseInstance {
  if (databaseUrl) {
    const needsSsl = /sslmode=require/i.test(databaseUrl);
    return {
      pool: new Pool({
        connectionString: databaseUrl,
        ssl: needsSsl ? { rejectUnauthorized: false } : undefined,
      }),
    };
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
