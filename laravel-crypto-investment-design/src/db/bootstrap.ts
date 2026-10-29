import { hashPassword } from "@/lib/password";
import { DEMO_ACCOUNT } from "@/lib/demo-access";

/**
 * One-shot schema bootstrap for a real Postgres database.
 *
 * The app is deployed to Vercel, where there is no CLI step between adding
 * DATABASE_URL and serving traffic. This runs the idempotent DDL once per
 * process (per lambda) so connecting a fresh database is enough — no
 * `drizzle-kit push` required. Every statement is CREATE ... IF NOT EXISTS or
 * an INSERT ... ON CONFLICT DO NOTHING, so it is safe to run repeatedly.
 */

type Runner = (text: string, values?: unknown[]) => Promise<unknown>;

const DDL = `
CREATE TABLE IF NOT EXISTS waitlist (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  email varchar(200) NOT NULL,
  plan varchar(40) NOT NULL DEFAULT 'starter',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS waitlist_email_idx ON waitlist (email);

CREATE TABLE IF NOT EXISTS messages (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  email varchar(200) NOT NULL,
  message text NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS users (
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
CREATE INDEX IF NOT EXISTS users_email_idx ON users (email);
CREATE UNIQUE INDEX IF NOT EXISTS users_referral_code_idx ON users (referral_code);

CREATE TABLE IF NOT EXISTS holdings (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  coin_id varchar(60) NOT NULL,
  symbol varchar(20) NOT NULL,
  amount numeric(28,10) NOT NULL DEFAULT '0',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS holdings_user_idx ON holdings (user_id);

CREATE TABLE IF NOT EXISTS transactions (
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
CREATE INDEX IF NOT EXISTS transactions_user_idx ON transactions (user_id);

CREATE TABLE IF NOT EXISTS sim_trades (
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
CREATE INDEX IF NOT EXISTS sim_trades_user_idx ON sim_trades (user_id);
CREATE INDEX IF NOT EXISTS sim_trades_status_idx ON sim_trades (status);

CREATE TABLE IF NOT EXISTS plans (
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
CREATE UNIQUE INDEX IF NOT EXISTS plans_slug_idx ON plans (slug);

CREATE TABLE IF NOT EXISTS investments (
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
CREATE INDEX IF NOT EXISTS investments_user_idx ON investments (user_id);
CREATE INDEX IF NOT EXISTS investments_status_idx ON investments (status);

CREATE TABLE IF NOT EXISTS deposit_requests (
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
CREATE INDEX IF NOT EXISTS deposit_requests_user_idx ON deposit_requests (user_id);
CREATE INDEX IF NOT EXISTS deposit_requests_status_idx ON deposit_requests (status);

CREATE TABLE IF NOT EXISTS withdrawal_requests (
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
CREATE INDEX IF NOT EXISTS withdrawal_requests_user_idx ON withdrawal_requests (user_id);
CREATE INDEX IF NOT EXISTS withdrawal_requests_status_idx ON withdrawal_requests (status);

CREATE TABLE IF NOT EXISTS ledger_accounts (
  id serial PRIMARY KEY,
  code varchar(60) NOT NULL UNIQUE,
  name varchar(120) NOT NULL,
  kind varchar(20) NOT NULL,
  normal varchar(10) NOT NULL,
  owner_user_id integer,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE UNIQUE INDEX IF NOT EXISTS ledger_accounts_code_idx ON ledger_accounts (code);

CREATE TABLE IF NOT EXISTS ledger_transactions (
  id serial PRIMARY KEY,
  kind varchar(30) NOT NULL,
  memo varchar(200) NOT NULL,
  ref_type varchar(30),
  ref_id integer,
  actor_id integer,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS ledger_entries (
  id serial PRIMARY KEY,
  txn_id integer NOT NULL REFERENCES ledger_transactions(id) ON DELETE CASCADE,
  account_id integer NOT NULL REFERENCES ledger_accounts(id),
  delta_usd numeric(18,2) NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS ledger_entries_txn_idx ON ledger_entries (txn_id);
CREATE INDEX IF NOT EXISTS ledger_entries_account_idx ON ledger_entries (account_id);

CREATE TABLE IF NOT EXISTS notifications (
  id serial PRIMARY KEY,
  user_id integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  title varchar(140) NOT NULL,
  body text NOT NULL,
  read_at timestamptz,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS notifications_user_idx ON notifications (user_id);

CREATE TABLE IF NOT EXISTS audit_log (
  id serial PRIMARY KEY,
  actor_id integer,
  actor_email varchar(200),
  action varchar(60) NOT NULL,
  target_type varchar(40),
  target_id integer,
  meta text,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS audit_log_action_idx ON audit_log (action);

CREATE TABLE IF NOT EXISTS kyc_submissions (
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

CREATE TABLE IF NOT EXISTS feature_flags (
  key varchar(60) PRIMARY KEY,
  enabled boolean NOT NULL DEFAULT true,
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS site_content (
  key varchar(80) PRIMARY KEY,
  value text NOT NULL,
  updated_by integer,
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS media_assets (
  id serial PRIMARY KEY,
  filename varchar(160) NOT NULL,
  mime varchar(60) NOT NULL,
  size_bytes integer NOT NULL,
  data text NOT NULL,
  uploaded_by integer,
  created_at timestamptz NOT NULL DEFAULT now()
);
`;

const FLAGS: Array<[string, boolean]> = [
  ["deposits", true],
  ["withdrawals", true],
  ["trading", true],
  ["plans", true],
  ["referrals", true],
  ["kyc", true],
];

const PLANS: Array<[string, string, string, string, string, string, number, number, number]> = [
  ["starter-vault", "Starter Vault", "A short first term to learn how daily accrual works.", "conservative", "100.00", "5000.00", 600, 30, 1],
  ["balanced-fund", "Balanced Growth", "A medium-term plan with weekly accrual.", "balanced", "500.00", "25000.00", 900, 90, 2],
  ["growth-engine", "Growth Engine", "A longer horizon with higher variability.", "growth", "1000.00", "50000.00", 1200, 180, 3],
  ["quant-desk", "Quant Desk", "Our highest target rate, for a full-year term.", "aggressive", "5000.00", "150000.00", 1500, 365, 4],
];

export async function runBootstrap(rawQuery: Runner): Promise<void> {
  await rawQuery(DDL);

  for (const [key, enabled] of FLAGS) {
    await rawQuery(
      `INSERT INTO feature_flags (key, enabled) VALUES ($1, $2) ON CONFLICT (key) DO NOTHING`,
      [key, enabled],
    );
  }

  for (const plan of PLANS) {
    await rawQuery(
      `INSERT INTO plans
        (slug, name, tagline, risk_level, min_amount_usd, max_amount_usd, annual_rate_bp, duration_days, sort_order)
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)
       ON CONFLICT (slug) DO NOTHING`,
      plan,
    );
  }

  // Super admin, seeded from the environment. Without this a fresh Postgres
  // database would have no way in.
  const adminEmail = (process.env.ADMIN_EMAIL ?? "admin@actuatrades.com").trim().toLowerCase();
  const adminHash =
    process.env.ADMIN_PASSWORD_HASH ?? (await hashPassword(cryptoRandom()));
  await rawQuery(
    `INSERT INTO users (name, email, password_hash, role, status, referral_code, kyc_status, profile_completed_at)
     VALUES ($1, $2, $3, 'owner', 'active', 'OWNER001', 'verified', now())
     ON CONFLICT (email) DO UPDATE SET role = 'owner'`,
    ["Platform Owner", adminEmail, adminHash],
  );

  // Reviewer account: an ordinary customer used for inspection.
  const reviewerHash = await hashPassword(DEMO_ACCOUNT.password);
  await rawQuery(
    `INSERT INTO users
       (name, email, password_hash, role, status, referral_code, kyc_status,
        phone, country, date_of_birth, address_line, city, postal_code, occupation,
        trading_experience, investment_goal, profile_completed_at)
     VALUES ($1, $2, $3, 'user', 'active', 'EN00007', 'verified',
             '+234 802 555 0144', 'Nigeria', '1991-04-18', '22 Olu Obasanjo Way',
             'Port Harcourt', '500272', 'Customer', 'intermediate', 'grow_savings', now())
     ON CONFLICT (email) DO NOTHING`,
    [DEMO_ACCOUNT.name, DEMO_ACCOUNT.email, reviewerHash],
  );
}

function cryptoRandom(): string {
  // Not imported from node:crypto at module scope so this file stays usable
  // in any runtime that provides globalThis.crypto.
  const bytes = new Uint8Array(24);
  globalThis.crypto.getRandomValues(bytes);
  return Array.from(bytes, (b) => b.toString(16).padStart(2, "0")).join("");
}
