import { randomBytes } from "node:crypto";
import { newDb, replaceQueryArgs$ } from "pg-mem";
import type { Pool } from "pg";
import { hashPassword } from "@/lib/password";
import { DEMO_ACCOUNT } from "@/lib/demo-access";

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
  referralCode: string;
  kycStatus?: string;
  profileCompleted?: boolean;
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
  balance_usd numeric(18,2) NOT NULL DEFAULT '0',
  referral_code varchar(16) NOT NULL UNIQUE,
  referred_by_id integer,
  kyc_status varchar(20) NOT NULL DEFAULT 'unverified',
  trade_outcome varchar(10) NOT NULL DEFAULT 'manual',
  phone varchar(40),
  country varchar(60),
  date_of_birth varchar(10),
  address_line varchar(200),
  city varchar(120),
  postal_code varchar(20),
  occupation varchar(120),
  trading_experience varchar(20),
  investment_goal varchar(30),
  profile_completed_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX users_email_idx ON users (email);
CREATE INDEX users_referral_code_idx ON users (referral_code);

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
  note text,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX transactions_user_idx ON transactions (user_id);

CREATE TABLE sim_trades (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  coin_id varchar(60) NOT NULL,
  symbol varchar(20) NOT NULL,
  direction varchar(6) NOT NULL,
  stake_usd numeric(18,2) NOT NULL,
  entry_price numeric(18,6) NOT NULL,
  exit_price numeric(18,6),
  payout_bp integer NOT NULL DEFAULT 8500,
  duration_minutes integer NOT NULL,
  status varchar(12) NOT NULL DEFAULT 'open',
  outcome_source varchar(12),
  payout_usd numeric(18,2),
  settled_by integer,
  settle_note text,
  opened_at timestamptz NOT NULL DEFAULT now(),
  expires_at timestamptz NOT NULL,
  settled_at timestamptz
);
CREATE INDEX sim_trades_user_idx ON sim_trades (user_id);
CREATE INDEX sim_trades_status_idx ON sim_trades (status);

CREATE TABLE plans (
  id serial PRIMARY KEY,
  slug varchar(40) NOT NULL UNIQUE,
  name varchar(80) NOT NULL,
  tagline varchar(200) NOT NULL,
  risk_level varchar(20) NOT NULL DEFAULT 'balanced',
  min_amount_usd numeric(18,2) NOT NULL,
  max_amount_usd numeric(18,2) NOT NULL,
  annual_rate_bp integer NOT NULL,
  duration_days integer NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'active',
  sort_order integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX plans_slug_idx ON plans (slug);

CREATE TABLE investments (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  plan_id integer NOT NULL REFERENCES plans(id),
  principal_usd numeric(18,2) NOT NULL,
  annual_rate_bp integer NOT NULL,
  duration_days integer NOT NULL,
  days_paid integer NOT NULL DEFAULT 0,
  earned_usd numeric(18,2) NOT NULL DEFAULT '0',
  status varchar(20) NOT NULL DEFAULT 'active',
  started_at timestamptz NOT NULL DEFAULT now(),
  ends_at timestamptz NOT NULL,
  last_accrued_at timestamptz
);
CREATE INDEX investments_user_idx ON investments (user_id);
CREATE INDEX investments_status_idx ON investments (status);

CREATE TABLE deposit_requests (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  amount_usd numeric(18,2) NOT NULL,
  method varchar(30) NOT NULL DEFAULT 'bank_transfer',
  reference varchar(80),
  status varchar(20) NOT NULL DEFAULT 'pending',
  admin_note text,
  reviewed_by integer,
  reviewed_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX deposit_requests_user_idx ON deposit_requests (user_id);
CREATE INDEX deposit_requests_status_idx ON deposit_requests (status);

CREATE TABLE withdrawal_requests (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  amount_usd numeric(18,2) NOT NULL,
  destination varchar(160) NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'pending',
  admin_note text,
  reviewed_by integer,
  reviewed_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX withdrawal_requests_user_idx ON withdrawal_requests (user_id);
CREATE INDEX withdrawal_requests_status_idx ON withdrawal_requests (status);

CREATE TABLE ledger_accounts (
  id serial PRIMARY KEY,
  code varchar(60) NOT NULL UNIQUE,
  name varchar(120) NOT NULL,
  kind varchar(20) NOT NULL,
  normal varchar(10) NOT NULL,
  owner_user_id integer,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX ledger_accounts_code_idx ON ledger_accounts (code);

CREATE TABLE ledger_transactions (
  id serial PRIMARY KEY,
  kind varchar(30) NOT NULL,
  memo varchar(200) NOT NULL,
  ref_type varchar(30),
  ref_id integer,
  actor_id integer,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE ledger_entries (
  id serial PRIMARY KEY,
  txn_id integer NOT NULL REFERENCES ledger_transactions(id) ON DELETE CASCADE,
  account_id integer NOT NULL REFERENCES ledger_accounts(id),
  delta_usd numeric(18,2) NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX ledger_entries_txn_idx ON ledger_entries (txn_id);
CREATE INDEX ledger_entries_account_idx ON ledger_entries (account_id);

CREATE TABLE notifications (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  title varchar(140) NOT NULL,
  body text NOT NULL,
  read_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX notifications_user_idx ON notifications (user_id);

CREATE TABLE audit_log (
  id serial PRIMARY KEY,
  actor_id integer,
  actor_email varchar(200),
  action varchar(60) NOT NULL,
  target_type varchar(40),
  target_id integer,
  meta text,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX audit_log_action_idx ON audit_log (action);

CREATE TABLE kyc_submissions (
  id serial PRIMARY KEY,
  user_id integer NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
  full_name varchar(140) NOT NULL,
  country varchar(80) NOT NULL,
  document_type varchar(40) NOT NULL,
  document_last4 varchar(4) NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'pending',
  review_note text,
  reviewed_by integer,
  reviewed_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE feature_flags (
  key varchar(60) PRIMARY KEY,
  enabled boolean NOT NULL DEFAULT true,
  updated_at timestamptz NOT NULL DEFAULT now()
);
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
    const rowMode = typeof config === "object" && config.rowMode === "array";
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

function isoMinutesAgo(minutes: number): string {
  return new Date(Date.now() - minutes * 60 * 1000).toISOString();
}

function isoMinutesAhead(minutes: number): string {
  return new Date(Date.now() + minutes * 60 * 1000).toISOString();
}

function isoDaysAhead(days: number): string {
  return new Date(Date.now() + days * 24 * 60 * 60 * 1000).toISOString();
}

/** Deterministic, collision-free referral codes for the seeded accounts. */
function seedCode(id: number): string {
  return `EN${String(id).padStart(5, "0")}`;
}

export function createDemoDatabase(): DemoDatabase {
  const mem = newDb();
  mem.public.none(DDL);
  const memoryPool = new MemoryPool(mem);
  const pool = memoryPool as unknown as Pool;

  const run = (text: string, values: unknown[] = []) =>
    memoryPool.run(text, values);

  const adminEmail = (process.env.ADMIN_EMAIL ?? "admin@enethi.app").toLowerCase();
  // If no hash is configured, use an unknowable random password rather than
  // shipping a public default credential.
  const adminPasswordHash =
    process.env.ADMIN_PASSWORD_HASH ?? hashPassword(randomBytes(32).toString("hex"));
  const lockedHash = hashPassword(randomBytes(32).toString("hex"));
  // The reviewer sign-in. It is an ordinary customer account.
  const demoPasswordHash = hashPassword(DEMO_ACCOUNT.password);

  // ── Admin ────────────────────────────────────────────────────────
  // Let the serial column assign id 1; seeding explicit ids does not advance
  // pg-mem's sequence and would break later registrations.
  run(
    `INSERT INTO users
      (name, email, password_hash, role, status, balance_usd, referral_code, kyc_status, created_at)
     VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)`,
    ["Platform Admin", adminEmail, adminPasswordHash, "admin", "active", "0.00", "ADMIN001", "verified", isoDaysAgo(120)],
  );

  // ── Seed clients (ids 2-6) ───────────────────────────────────────
  const demoUsers: Array<[number, string, string, string, string, string]> = [
    [2, "Tunde Bakare", "tunde.bakare@example.com", "active", "13420.50", "verified"],
    [3, "Priya Raman", "priya.raman@example.com", "active", "9275.00", "unverified"],
    [4, "Mateo Silva", "mateo.silva@example.com", "active", "26200.75", "pending"],
    [5, "Hannah Weber", "hannah.weber@example.com", "suspended", "1500.00", "unverified"],
    [6, "Chidi Nwosu", "chidi.nwosu@example.com", "active", "10000.00", "unverified"],
    // Reviewer account seeded with the password in src/lib/demo-access.
    [7, DEMO_ACCOUNT.name, DEMO_ACCOUNT.email, "active", "24562.50", "verified"],
  ];

  for (const [id, name, email, status, balance, kyc] of demoUsers) {
    run(
      `INSERT INTO users
        (name, email, password_hash, role, status, balance_usd, referral_code, kyc_status, created_at)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)`,
      [
        name,
        email,
        email === DEMO_ACCOUNT.email ? demoPasswordHash : lockedHash,
        "user",
        status,
        balance,
        seedCode(id),
        kyc,
        isoDaysAgo(90 - id * 7),
      ],
    );
  }

  // Seeded customers already have complete profiles so the reviewer account
  // opens straight onto the dashboard.
  const seedProfiles: Array<[string, string, string, string, string, string, string, string, string]> = [
    [seedCode(2), "+44 20 7946 0102", "United Kingdom", "1988-03-14", "48 Deansgate", "Manchester", "M3 2BW", "intermediate", "grow_savings"],
    [seedCode(3), "+91 22 5550 1180", "India", "1992-11-02", "12 Marine Drive", "Mumbai", "400020", "experienced", "short_term"],
    [seedCode(4), "+55 11 9555 0143", "Brazil", "1985-07-21", "Av. Paulista 900", "Sao Paulo", "01310-100", "beginner", "retirement"],
    [seedCode(5), "+49 30 5550 1777", "Germany", "1990-01-09", "Torstrasse 14", "Berlin", "10119", "none", "learn"],
    [seedCode(6), "+234 803 555 0199", "Nigeria", "1996-09-30", "7 Aba Road", "Port Harcourt", "500001", "beginner", "grow_savings"],
    [seedCode(7), "+234 802 555 0144", "Nigeria", "1991-04-18", "22 Olu Obasanjo Way", "Port Harcourt", "500272", "intermediate", "grow_savings"],
  ];
  for (const [code, phone, country, dob, address, city, postal, experience, goal] of seedProfiles) {
    run(
      `UPDATE users SET phone = $1, country = $2, date_of_birth = $3, address_line = $4,
              city = $5, postal_code = $6, trading_experience = $7, investment_goal = $8,
              occupation = 'Customer', profile_completed_at = $9
        WHERE referral_code = $10`,
      [phone, country, dob, address, city, postal, experience, goal, isoDaysAgo(20), code],
    );
  }

  // Referral relationships: 3 and 6 were referred by 2.
  run(`UPDATE users SET referred_by_id = (SELECT id FROM users WHERE referral_code = $1) WHERE referral_code = $2`, [seedCode(2), seedCode(3)]);
  run(`UPDATE users SET referred_by_id = (SELECT id FROM users WHERE referral_code = $1) WHERE referral_code = $2`, [seedCode(2), seedCode(6)]);

  // ── Seeded portfolios ────────────────────────────────────────────
  // Quantity, plus the cost basis used for the opening ledger journal.
  const holdings: Array<[string, string, string, string, string]> = [
    [seedCode(2), "bitcoin", "BTC", "0.1284000000", "7960.80"],
    [seedCode(2), "ethereum", "ETH", "1.8500000000", "4440.00"],
    [seedCode(3), "solana", "SOL", "42.5000000000", "4250.00"],
    [seedCode(4), "bitcoin", "BTC", "0.3120000000", "19344.00"],
    [seedCode(6), "ethereum", "ETH", "0.4500000000", "1080.00"],
    // Public demo account portfolio (cost basis only; live value comes from prices).
    [seedCode(7), "bitcoin", "BTC", "0.1200000000", "10009.35"],
    [seedCode(7), "ethereum", "ETH", "1.4000000000", "3648.33"],
    [seedCode(7), "solana", "SOL", "20.0000000000", "2329.00"],
  ];
  for (const [code, coinId, symbol, amount] of holdings) {
    run(
      `INSERT INTO holdings (user_id, coin_id, symbol, amount, created_at)
       VALUES ((SELECT id FROM users WHERE referral_code = $1), $2, $3, $4, $5)`,
      [code, coinId, symbol, amount, isoDaysAgo(30)],
    );
  }

  const txns: Array<[string, string, string | null, string | null, string, number]> = [
    [seedCode(2), "deposit", null, null, "10000.00", 60],
    [seedCode(2), "buy", "bitcoin", "BTC", "2500.00", 45],
    [seedCode(2), "plan_deposit", null, null, "5000.00", 20],
    [seedCode(3), "deposit", null, null, "5000.00", 40],
    [seedCode(3), "buy", "solana", "SOL", "1200.00", 30],
    [seedCode(4), "deposit", null, null, "20000.00", 70],
    [seedCode(4), "buy", "bitcoin", "BTC", "7500.00", 55],
    [seedCode(6), "deposit", null, null, "10000.00", 15],
    [seedCode(6), "buy", "ethereum", "ETH", "850.00", 12],
  ];
  for (const [code, type, coinId, symbol, usdValue, daysAgo] of txns) {
    run(
      `INSERT INTO transactions (user_id, type, coin_id, symbol, usd_value, created_at)
       VALUES ((SELECT id FROM users WHERE referral_code = $1), $2, $3, $4, $5, $6)`,
      [code, type, coinId, symbol, usdValue, isoDaysAgo(daysAgo)],
    );
  }

  // ── Reviewer account: activity and trades ────────────────────────
  // One settled win, one settled loss and one live trade so the trade desk
  // screen has something real to look at on first load.
  // Notes go in as bound parameters: the demo driver's argument replacer treats
  // a literal "$150.00" in SQL text as placeholder number 150 and blows up.
  run(
    `INSERT INTO transactions (user_id, type, coin_id, symbol, usd_value, note, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'deposit', NULL, NULL, '25000.00', $2, $3)`,
    [
      seedCode(7),
      "Opening balance credited on account approval",
      isoDaysAgo(40),
    ],
  );
  run(
    `INSERT INTO transactions (user_id, type, coin_id, symbol, usd_value, note, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'trade_loss', 'solana', 'SOL', '150.00', $2, $3)`,
    [
      seedCode(7),
      "Lost SOL long · stake $150.00 · operator decision: Closed below the entry price at expiry.",
      isoDaysAgo(4),
    ],
  );
  run(
    `INSERT INTO transactions (user_id, type, coin_id, symbol, usd_value, note, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'trade_win', 'ethereum', 'ETH', '212.50', $2, $3)`,
    [
      seedCode(7),
      "Won ETH short · payout $212.50 · settled at market: Settled at the live market price at expiry.",
      isoDaysAgo(2),
    ],
  );
  run(
    `INSERT INTO transactions (user_id, type, coin_id, symbol, usd_value, note, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'trade_open', 'bitcoin', 'BTC', '500.00', $2, $3)`,
    [seedCode(7), "Long BTC · stake $500.00 · entry $83411.24", isoMinutesAgo(20)],
  );

  // won (2 days ago) → lost (4 days ago) → open (20 minutes ago)
  run(
    `INSERT INTO sim_trades
      (user_id, coin_id, symbol, direction, stake_usd, entry_price, exit_price, payout_bp,
       duration_minutes, status, outcome_source, payout_usd, settle_note, opened_at, expires_at, settled_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'ethereum', 'ETH', 'short', '250.00',
             '2650.100000', '2612.400000', 8500, 60, 'won', 'market', '212.50',
             'Settled at the live market price at expiry.', $2, $3, $4)`,
    [seedCode(7), isoDaysAgo(2), isoDaysAgo(2), isoDaysAgo(2)],
  );
  run(
    `INSERT INTO sim_trades
      (user_id, coin_id, symbol, direction, stake_usd, entry_price, exit_price, payout_bp,
       duration_minutes, status, outcome_source, payout_usd, settle_note, opened_at, expires_at, settled_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'solana', 'SOL', 'long', '150.00',
             '118.000000', '114.200000', 8500, 15, 'lost', 'operator', '0.00',
             'Closed below the entry price at expiry.', $2, $3, $4)`,
    [seedCode(7), isoDaysAgo(4), isoDaysAgo(4), isoDaysAgo(4)],
  );
  run(
    `INSERT INTO sim_trades
      (user_id, coin_id, symbol, direction, stake_usd, entry_price, exit_price, payout_bp,
       duration_minutes, status, opened_at, expires_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), 'bitcoin', 'BTC', 'long', '500.00',
             '83411.240000', NULL, 8500, 60, 'open', $2, $3)`,
    [seedCode(7), isoMinutesAgo(20), isoMinutesAhead(40)],
  );

  // ── Plans ────────────────────────────────────────────────────────
  const plans: Array<[string, string, string, string, string, string, number, number, number]> = [
    ["starter-vault", "Starter Vault", "A short first term to learn how daily accrual works.", "conservative", "100.00", "5000.00", 600, 30, 1],
    ["balanced-fund", "Balanced Growth", "A medium-term plan with weekly accrual.", "balanced", "500.00", "25000.00", 900, 90, 2],
    ["growth-engine", "Growth Engine", "A longer horizon with higher variability.", "growth", "1000.00", "50000.00", 1200, 180, 3],
    ["quant-desk", "Quant Desk", "Our highest target rate, for a full-year term.", "aggressive", "5000.00", "150000.00", 1500, 365, 4],
  ];
  for (const [slug, name, tagline, risk, min, max, bp, days, order] of plans) {
    run(
      `INSERT INTO plans (slug, name, tagline, risk_level, min_amount_usd, max_amount_usd, annual_rate_bp, duration_days, sort_order)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)`,
      [slug, name, tagline, risk, min, max, bp, days, order],
    );
  }

  // ── Plans in flight ──────────────────────────────────────────────
  run(
    `INSERT INTO investments
      (user_id, plan_id, principal_usd, annual_rate_bp, duration_days, days_paid, earned_usd, status, started_at, ends_at, last_accrued_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1),
             (SELECT id FROM plans WHERE slug = $2),
             $3, $4, $5, $6, $7, 'active', $8, $9, $10)`,
    [seedCode(2), "balanced-fund", "5000.00", 900, 90, 11, "13.56", isoDaysAgo(11), isoDaysAhead(79), isoDaysAgo(0)],
  );
  run(
    `INSERT INTO investments
      (user_id, plan_id, principal_usd, annual_rate_bp, duration_days, days_paid, earned_usd, status, started_at, ends_at, last_accrued_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1),
             (SELECT id FROM plans WHERE slug = $2),
             $3, $4, $5, $6, $7, 'completed', $8, $9, $10)`,
    [seedCode(4), "starter-vault", "2000.00", 600, 30, 30, "9.86", isoDaysAgo(45), isoDaysAgo(15), isoDaysAgo(15)],
  );

  // ── Review queues ────────────────────────────────────────────────
  run(
    `INSERT INTO deposit_requests (user_id, amount_usd, method, reference, status, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), $2, 'bank_transfer', $3, 'pending', $4)`,
    [seedCode(6), "2500.00", "SIM-8H2K4Q", isoDaysAgo(1)],
  );
  run(
    `INSERT INTO withdrawal_requests (user_id, amount_usd, destination, status, created_at)
     VALUES ((SELECT id FROM users WHERE referral_code = $1), $2, $3, 'pending', $4)`,
    [seedCode(3), "750.00", "sim-wallet: 0x8Fc2…9a41", isoDaysAgo(2)],
  );

  // ── Notifications ────────────────────────────────────────────────
  const notes: Array<[string, string, string, number]> = [
    [seedCode(2), "Plan accrual posted", "Your Balanced Growth plan credited earnings for day 11.", 0],
    [seedCode(2), "Referral reward", "Priya Raman joined with your code. Commission credited.", 3],
    [seedCode(6), "Deposit under review", "Your deposit request is queued for review.", 1],
  ];
  for (const [code, title, body, daysAgo] of notes) {
    run(
      `INSERT INTO notifications (user_id, title, body, created_at)
       VALUES ((SELECT id FROM users WHERE referral_code = $1), $2, $3, $4)`,
      [code, title, body, isoDaysAgo(daysAgo)],
    );
  }

  // ── Chart of accounts ────────────────────────────────────────────
  const accounts: Array<[string, string, string, string, number | null]> = [
    ["asset:cash", "Custody cash", "asset", "debit", null],
    ["asset:crypto-held", "Crypto held for users", "asset", "debit", null],
    ["revenue:fees", "Trading fees", "revenue", "credit", null],
    ["expense:simulated-yield", "Plan yield", "expense", "debit", null],
    ["expense:referral-bonus", "Referral bonuses", "expense", "debit", null],
    ["revenue:trade-settlement", "Forfeited stakes", "revenue", "credit", null],
    ["expense:trade-payout", "Winning payouts", "expense", "debit", null],
    ["equity:rounding", "Rounding", "equity", "credit", null],
  ];
  for (const [code, name, kind, normal, owner] of accounts) {
    run(
      `INSERT INTO ledger_accounts (code, name, kind, normal, owner_user_id)
       VALUES ($1, $2, $3, $4, $5)`,
      [code, name, kind, normal, owner],
    );
  }

  // Per-user accounts, then balanced opening journals that reproduce the
  // seeded cash, crypto-at-cost and locked principal. Debits always equal
  // credits so the trial balance starts at zero.
  const seedCash: Record<string, string> = {
    [seedCode(2)]: "13420.50",
    [seedCode(3)]: "9275.00",
    [seedCode(4)]: "26200.75",
    [seedCode(5)]: "1500.00",
    [seedCode(6)]: "10000.00",
  };
  const seedLocked: Record<string, string> = {
    [seedCode(2)]: "5000.00",
    [seedCode(4)]: "2000.00",
  };

  const seedUserIds: Record<string, number> = {
    [seedCode(2)]: 2,
    [seedCode(3)]: 3,
    [seedCode(4)]: 4,
    [seedCode(5)]: 5,
    [seedCode(6)]: 6,
    [seedCode(7)]: 7,
  };

  for (const [code, userId] of Object.entries(seedUserIds)) {
    for (const [suffix, label] of [
      ["cash", "available cash"],
      ["crypto", "crypto at cost"],
      ["locked", "principal in plans"],
      ["stake", "stake in open trades"],
    ] as const) {
      run(
        `INSERT INTO ledger_accounts (code, name, kind, normal, owner_user_id)
         VALUES ($1, $2, 'liability', 'credit', $3)`,
        [`liability:user:${userId}:${suffix}`, `User ${userId} ${label}`, userId],
      );
    }
    void code;
  }

  // Opening deposit: fund the custody asset and the user's cash liability.
  run(
    `INSERT INTO ledger_transactions (kind, memo) VALUES ('seed:opening', 'Seeded opening balances')`,
  );
  const seedTxnId = `(SELECT id FROM ledger_transactions WHERE kind = 'seed:opening')`;
  for (const [code, amount] of Object.entries(seedCash)) {
    const total = Number(amount) + Number(seedLocked[code] ?? 0);
    const totalStr = total.toFixed(2);
    run(
      `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
       VALUES (${seedTxnId}, (SELECT id FROM ledger_accounts WHERE code = 'asset:cash'), $1)`,
      [totalStr],
    );
    const userId = seedUserIds[code];
    run(
      `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
       VALUES (${seedTxnId}, (SELECT id FROM ledger_accounts WHERE code = $1), $2)`,
      [`liability:user:${userId}:cash`, `-${amount}`],
    );
    if (seedLocked[code]) {
      run(
        `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
         VALUES (${seedTxnId}, (SELECT id FROM ledger_accounts WHERE code = $1), $2)`,
        [`liability:user:${userId}:locked`, `-${seedLocked[code]}`],
      );
    }
  }

  // Crypto held for users: debit the custody asset, credit each user's
  // crypto-at-cost liability. Balanced in aggregate.
  run(
    `INSERT INTO ledger_transactions (kind, memo) VALUES ('seed:holdings', 'Seeded crypto at cost')`,
  );
  const holdingsTxnId = `(SELECT id FROM ledger_transactions WHERE kind = 'seed:holdings')`;
  for (const [code, , , , cost] of holdings) {
    const userId = seedUserIds[code];
    run(
      `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
       VALUES (${holdingsTxnId}, (SELECT id FROM ledger_accounts WHERE code = 'asset:crypto-held'), $1)`,
      [cost],
    );
    run(
      `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
       VALUES (${holdingsTxnId}, (SELECT id FROM ledger_accounts WHERE code = $1), $2)`,
      [`liability:user:${userId}:crypto`, `-${cost}`],
    );
  }

  // ── Reviewer account journals ────────────────────────────────────
  // Built leg-by-leg so the numbers on the public demo account are exact:
  // open with $25,000, lose a $150 SOL stake, win $212.50 on an ETH short and
  // leave $500 escrowed in a live BTC trade → $24,562.50 available cash.
  const demoUserId = seedUserIds[seedCode(7)];
  const seedJournal = (kind: string, memo: string, legs: Array<[string, string]>) => {
    const inserted = run(
      `INSERT INTO ledger_transactions (kind, memo) VALUES ($1, $2) RETURNING id`,
      [kind, memo],
    ) as { rows: Array<{ id: number }> };
    const txnId = Number(inserted.rows[0]?.id);
    for (const [code, delta] of legs) {
      run(
        `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
         VALUES ($1, (SELECT id FROM ledger_accounts WHERE code = $2), $3)`,
        [txnId, code, delta],
      );
    }
  };

  const demoCash = `liability:user:${demoUserId}:cash`;
  const demoStake = `liability:user:${demoUserId}:stake`;

  seedJournal("seed:opening", "Reviewer account opening balance", [
    ["asset:cash", "25000.00"],
    [demoCash, "-25000.00"],
  ]);

  // Lost SOL long: stake escrowed, then forfeited to house revenue.
  seedJournal("seed:trade-lost.open", "Trade opened: long SOL stake", [
    [demoCash, "150.00"],
    [demoStake, "-150.00"],
  ]);
  seedJournal("seed:trade-lost.settle", "Trade settled: SOL long lost", [
    [demoStake, "150.00"],
    ["revenue:trade-settlement", "-150.00"],
  ]);

  // Won ETH short: stake escrowed, then returned with $212.50 of house payout.
  seedJournal("seed:trade-won.open", "Trade opened: short ETH stake", [
    [demoCash, "250.00"],
    [demoStake, "-250.00"],
  ]);
  seedJournal("seed:trade-won.settle", "Trade settled: ETH short won", [
    [demoStake, "250.00"],
    ["expense:trade-payout", "212.50"],
    [demoCash, "-462.50"],
  ]);

  // Live BTC long: $500 still in escrow.
  seedJournal("seed:trade-open", "Trade opened: long BTC stake", [
    [demoCash, "500.00"],
    [demoStake, "-500.00"],
  ]);

  // Feature flags
  const flags: Array<[string, boolean]> = [
    ["plans", true],
    ["deposits", true],
    ["withdrawals", true],
    ["referrals", true],
    ["kyc", true],
  ];
  for (const [key, enabled] of flags) {
    run(`INSERT INTO feature_flags (key, enabled) VALUES ($1, $2)`, [key, enabled]);
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
        (name, email, password_hash, role, status, balance_usd, referral_code, kyc_status,
         profile_completed_at, phone, country, date_of_birth, address_line, city, postal_code,
         occupation, trading_experience, investment_goal, created_at)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19)
       RETURNING id`,
      [
        user.name.slice(0, 120),
        email,
        email === DEMO_ACCOUNT.email ? demoPasswordHash : lockedHash,
        user.role,
        user.status,
        user.balanceUsd,
        user.referralCode.slice(0, 16),
        user.kycStatus ?? "unverified",
        user.profileCompleted ?? true,
        user.profileCompleted === false ? null : "+1 555 0100",
        user.profileCompleted === false ? null : "United States",
        user.profileCompleted === false ? null : "1990-01-01",
        user.profileCompleted === false ? null : "1 Market Street",
        user.profileCompleted === false ? null : "New York",
        user.profileCompleted === false ? null : "10001",
        user.profileCompleted === false ? null : "Product tester",
        user.profileCompleted === false ? null : "intermediate",
        user.profileCompleted === false ? null : "grow_savings",
        new Date().toISOString(),
      ],
    );
    const localId = Number((inserted.rows as Array<{ id: number }>)[0]?.id);
    if (!Number.isInteger(localId)) return;
    const now = new Date().toISOString();

    // Re-create a starter portfolio and the matching balanced ledger legs so
    // the demo books stay consistent on a cold start.
    await memoryPool.query(
      `INSERT INTO transactions (user_id, type, usd_value, created_at)
       VALUES ($1, $2, $3, $4)`,
      [localId, "opening_credit", user.balanceUsd, now],
    );

    for (const suffix of ["cash", "crypto", "locked"]) {
      await memoryPool.query(
        `INSERT INTO ledger_accounts (code, name, kind, normal, owner_user_id)
         VALUES ($1, $2, 'liability', 'credit', $3)`,
        [`liability:user:${localId}:${suffix}`, `User ${localId} ${suffix}`, localId],
      );
    }
    const txn = await memoryPool.query(
      `INSERT INTO ledger_transactions (kind, memo) VALUES ('credit:opening', $1) RETURNING id`,
      [`Registration seed for ${email}`],
    );
    const txnId = Number((txn.rows as Array<{ id: number }>)[0]?.id);
    if (Number.isInteger(txnId)) {
      for (const [code, amount] of [
        ["asset:cash", user.balanceUsd],
        [`liability:user:${localId}:cash`, `-${user.balanceUsd}`],
      ] as const) {
        const acct = await memoryPool.query(
          "SELECT id FROM ledger_accounts WHERE code = $1",
          [code],
        );
        const accountId = Number((acct.rows as Array<{ id: number }>)[0]?.id);
        if (Number.isInteger(accountId)) {
          await memoryPool.query(
            `INSERT INTO ledger_entries (txn_id, account_id, delta_usd)
             VALUES ($1, $2, $3)`,
            [txnId, accountId, amount],
          );
        }
      }
    }
  }

  return { pool, ensureUser };
}
