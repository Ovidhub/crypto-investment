import { drizzle, type NodePgDatabase } from "drizzle-orm/node-postgres";
import { Client, Pool } from "pg";
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
  // `next build` collects page data before any traffic is served. There is no
  // reason for a build to open a connection, so builds always use the defaults.
  if (process.env.NEXT_PHASE === "phase-production-build") return undefined;
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

/** Upper bound on the one-shot schema bootstrap. */
const BOOTSTRAP_TIMEOUT_MS = 25_000;

type DatabaseInstance = {
  pool: Pool;
  ensureUser?: (user: DemoUser) => Promise<void>;
};

const globalForDb = globalThis as typeof globalThis & {
  __enethiDatabase?: DatabaseInstance;
};

function sslOptions(url: string) {
  const needsSsl = /sslmode=require|ssl=true|neon\.tech|supabase|prisma\.io|vercel-storage/i.test(
    url,
  );
  return needsSsl ? { rejectUnauthorized: false } : undefined;
}

/**
 * Run the idempotent DDL once per process, on a dedicated connection.
 *
 * This deliberately does not borrow from the pool. Every pooled query waits for
 * the bootstrap to finish, so a bootstrap that borrowed a pooled connection
 * would wait on itself and hang forever.
 */
function startBootstrap(url: string): Promise<void> {
  return (async () => {
    const client = new Client({
      connectionString: url,
      ssl: sslOptions(url),
      connectionTimeoutMillis: 10_000,
      statement_timeout: 20_000,
      query_timeout: 25_000,
    });
    const deadline = new Promise<void>((resolve) =>
      setTimeout(resolve, BOOTSTRAP_TIMEOUT_MS),
    );
    try {
      await client.connect();
      await Promise.race([
        runBootstrap((text, values) => client.query(text, values as never[])),
        deadline,
      ]);
    } catch (error) {
      // Never take the site down because of a bootstrap hiccup: log it and let
      // each request surface its own error if a table really is missing.
      console.error("Schema bootstrap failed", error);
    } finally {
      await client.end().catch(() => undefined);
    }
  })();
}

/**
 * The app's pool. Queries wait for the schema bootstrap, then go straight to
 * the underlying pg pool; the pool itself is left untouched so that its own
 * internals (which re-enter `connect`) keep working.
 */
function createInstance(): DatabaseInstance {
  if (!databaseUrl) return createDemoDatabase();

  const base = new Pool({
    connectionString: databaseUrl,
    ssl: sslOptions(databaseUrl),
    max: 5,
    idleTimeoutMillis: 10_000,
    connectionTimeoutMillis: 10_000,
    statement_timeout: 20_000,
    query_timeout: 25_000,
  });

  const ready = startBootstrap(databaseUrl);

  const pool = {
    query: (async (...args: Parameters<Pool["query"]>) => {
      await ready;
      return (base.query as (...a: Parameters<Pool["query"]>) => unknown)(...args);
    }) as Pool["query"],
    connect: (async () => {
      await ready;
      return base.connect();
    }) as Pool["connect"],
    end: () => base.end(),
  } as unknown as Pool;

  return { pool };
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
