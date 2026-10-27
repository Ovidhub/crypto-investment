import { randomBytes } from "node:crypto";
import { newDb, replaceQueryArgs$ } from "pg-mem";
import type { Pool } from "pg";
import { hashPassword } from "@/lib/password";

/**
 * A tiny in-process Postgres used when no DATABASE_URL is configured.
 *
 * This makes the app deployable to Vercel as a working demo without a separate
 * database service. Data lives in the server process only: it is reset on cold
 * starts and is not shared between separate function instances. Set
 * DATABASE_URL to a real Postgres database for persistent production data.
 */
export type DemoUser = {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  balanceUsd: string;
};

export type DemoDatabase = {
  pool: Pool;
  ensureUser: (user: DemoUser) => Promise<void>;
};

const DDL = `
CREATE TABLE waitlist (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  email varchar(200) NOT NULL,
  plan varchar(40) NOT NULL DEFAULT 'starter',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX waitlist_email_idx ON waitlist (email);

CREATE TABLE messages (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  email varchar(200) NOT NULL,
  message text NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE users (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  email varchar(200) NOT NULL UNIQUE,
  password_hash text NOT NULL,
  role varchar(20) NOT NULL DEFAULT 'user',
  status varchar(20) NOT NULL DEFAULT 'active',
  balance_usd numeric(18,2) NOT NULL DEFAULT '10000',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX users_email_idx ON users (email);

CREATE TABLE holdings (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  coin_id varchar(60) NOT NULL,
  symbol varchar(20) NOT NULL,
  amount numeric(28,10) NOT NULL DEFAULT '0',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX holdings_user_idx ON holdings (user_id);

CREATE TABLE transactions (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  type varchar(20) NOT NULL,
  coin_id varchar(60),
  symbol varchar(20),
  amount numeric(28,10),
  price numeric(18,6),
  usd_value numeric(18,2) NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX transactions_user_idx ON transactions (user_id);
`;

type QueryConfigLike = {
  text: string;
  values?: unknown[];
  rowMode?: string;
};

class MemoryPool {
  constructor(private readonly mem: ReturnType<typeof newDb>) {}

  async query(
    config: string | QueryConfigLike,
    params?: unknown[],
  ): Promise<Record<string, unknown>> {
    const text = typeof config === "string" ? config : config.text;
    const values =
      Array.isArray(params) && params.length > 0
        ? params
        : typeof config === "object"
          ? config.values ?? []
          : [];
    const queryText = values.length
      ? replaceQueryArgs$(
          text,
          values as Parameters<typeof replaceQueryArgs$>[1],
        )
      : text;
    const result = this.mem.public.query(queryText) as unknown as {
      rows: Record<string, unknown>[];
      fields?: { name: string }[];
      [key: string]: unknown;
    };
    const rowMode =
      typeof config === "object" && config.rowMode === "array";
    const rows = rowMode
      ? result.rows.map((row) =>
          (result.fields ?? []).map((field) => row[field.name]),
        )
      : result.rows;
    return { ...result, rows };
  }

  /** Synchronous helper used only while seeding the in-memory database. */
  run(text: string, values: unknown[] = []) {
    const queryText = values.length
      ? replaceQueryArgs$(
          text,
          values as Parameters<typeof replaceQueryArgs$>[1],
        )
      : text;
    return this.mem.public.query(queryText);
  }

  async connect() {
    return {
      query: (config: string | QueryConfigLike, params?: unknown[]) =>
        this.query(config, params),
      release: () => undefined,
    };
  }

  async end() {
    return undefined;
  }
}

function isoDaysAgo(days: number): string {
  return new Date(Date.now() - days * 24 * 60 * 60 * 1000).toISOString();
}

export function createDemoDatabase(): DemoDatabase {
  const mem = newDb();
  mem.public.none(DDL);
  const memoryPool = new MemoryPool(mem);
  const pool = memoryPool as unknown as Pool;

  const run = (text: string, values: unknown[] = []) =>
    memoryPool.run(text, values);

  const adminEmail = (
    process.env.ADMIN_EMAIL ?? "admin@enethi.app"
  ).toLowerCase();
  // If no hash is configured, use an unknowable random password rather than
  // shipping a public default credential.
  const adminPasswordHash =
    process.env.ADMIN_PASSWORD_HASH ??
    hashPassword(randomBytes(32).toString("hex"));
  const lockedHash = hashPassword(randomBytes(32).toString("hex"));

  // The admin account is seeded identically in every function instance so the
  // admin login works regardless of which instance serves a request. Let the
  // serial column assign id 1; seeding explicit ids does not advance pg-mem's
  // sequence and would break later registrations.
  run(
    `INSERT INTO users
      (name, email, password_hash, role, status, balance_usd, created_at)
     VALUES ($1, $2, $3, $4, $5, $6, $7)`,
    [
      "Platform Admin",
      adminEmail,
      adminPasswordHash,
      "admin",
      "active",
      "250000.00",
      isoDaysAgo(120),
    ],
  );

  // These users receive ids 2-6 from the serial sequence (admin is id 1).
  const demoUsers: Array<[number, string, string, string, string]> = [
    [2, "Demo Investor", "demo.investor@enethi.app", "active", "18420.50"],
    [3, "Demo Trader", "demo.trader@enethi.app", "active", "9275.00"],
    [4, "Demo Holder", "demo.holder@enethi.app", "active", "31200.75"],
    [5, "Demo Reviewer", "demo.reviewer@enethi.app", "suspended", "1500.00"],
    [6, "Demo Newbie", "demo.newbie@enethi.app", "active", "10000.00"],
  ];

  for (const [id, name, email, status, balance] of demoUsers) {
    run(
      `INSERT INTO users
        (name, email, password_hash, role, status, balance_usd, created_at)
       VALUES ($1, $2, $3, $4, $5, $6, $7)`,
      [name, email, lockedHash, "user", status, balance, isoDaysAgo(90 - id * 7)],
    );
  }

  const holdings: Array<[number, string, string, string]> = [
    [2, "bitcoin", "BTC", "0.1284000000"],
    [2, "ethereum", "ETH", "1.8500000000"],
    [3, "solana", "SOL", "42.5000000000"],
    [4, "bitcoin", "BTC", "0.3120000000"],
    [6, "ethereum", "ETH", "0.4500000000"],
  ];
  for (const [userId, coinId, symbol, amount] of holdings) {
    run(
      `INSERT INTO holdings (user_id, coin_id, symbol, amount, created_at)
       VALUES ($1, $2, $3, $4, $5)`,
      [userId, coinId, symbol, amount, isoDaysAgo(30)],
    );
  }

  const transactions: Array<[number, string, string | null, string | null, string]> = [
    [2, "deposit", null, null, "10000.00"],
    [2, "buy", "bitcoin", "BTC", "2500.00"],
    [3, "deposit", null, null, "5000.00"],
    [3, "buy", "solana", "SOL", "1200.00"],
    [4, "deposit", null, null, "20000.00"],
    [4, "buy", "bitcoin", "BTC", "7500.00"],
    [6, "deposit", null, null, "10000.00"],
    [6, "buy", "ethereum", "ETH", "850.00"],
  ];
  for (const [userId, type, coinId, symbol, usdValue] of transactions) {
    run(
      `INSERT INTO transactions
        (user_id, type, coin_id, symbol, usd_value, created_at)
       VALUES ($1, $2, $3, $4, $5, $6)`,
      [userId, type, coinId, symbol, usdValue, isoDaysAgo(14 - userId)],
    );
  }

  const waitlist: Array<[number, string, string, string]> = [
    [1, "Demo Signup One", "waitlist.one@example.com", "starter"],
    [2, "Demo Signup Two", "waitlist.two@example.com", "pro"],
    [3, "Demo Signup Three", "waitlist.three@example.com", "whale"],
    [4, "Demo Signup Four", "waitlist.four@example.com", "starter"],
  ];
  for (const [id, name, email, plan] of waitlist) {
    run(
      `INSERT INTO waitlist (name, email, plan, created_at)
       VALUES ($1, $2, $3, $4)`,
      [name, email, plan, isoDaysAgo(10 - id)],
    );
  }

  async function ensureUser(user: DemoUser): Promise<void> {
    const email = user.email.trim().toLowerCase().slice(0, 200);
    const existing = await memoryPool.query(
      "SELECT id FROM users WHERE email = $1",
      [email],
    );
    if ((existing.rows as unknown[]).length > 0) return;

    // Insert without an explicit id: each function instance owns its own
    // sequence, so the cookie's id is not portable. getCurrentUser() resolves
    // demo sessions by email instead.
    const inserted = await memoryPool.query(
      `INSERT INTO users
        (name, email, password_hash, role, status, balance_usd, created_at)
       VALUES ($1, $2, $3, $4, $5, $6, $7)
       RETURNING id`,
      [
        user.name.slice(0, 120),
        email,
        lockedHash,
        user.role,
        user.status,
        user.balanceUsd,
        new Date().toISOString(),
      ],
    );
    const localId = Number((inserted.rows as Array<{ id: number }>)[0]?.id);
    if (!Number.isInteger(localId)) return;
    const now = new Date().toISOString();

    await memoryPool.query(
      `INSERT INTO holdings (user_id, coin_id, symbol, amount, created_at)
       VALUES ($1, $2, $3, $4, $5), ($6, $7, $8, $9, $10)`,
      [
        localId,
        "bitcoin",
        "BTC",
        "0.0500000000",
        now,
        localId,
        "ethereum",
        "ETH",
        "1.2000000000",
        now,
      ],
    );
    await memoryPool.query(
      `INSERT INTO transactions (user_id, type, usd_value, created_at)
       VALUES ($1, $2, $3, $4)`,
      [localId, "deposit", "10000.00", now],
    );
  }

  return { pool, ensureUser };
}
