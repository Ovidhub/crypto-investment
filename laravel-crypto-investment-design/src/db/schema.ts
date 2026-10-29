import {
  pgTable,
  serial,
  text,
  varchar,
  timestamp,
  index,
  integer,
  numeric,
  uniqueIndex,
  boolean,
} from "drizzle-orm/pg-core";

// ── Marketing: early-access waitlist ────────────────────────────────
export const waitlist = pgTable(
  "waitlist",
  {
    id: serial("id").primaryKey(),
    name: varchar("name", { length: 120 }).notNull(),
    email: varchar("email", { length: 200 }).notNull(),
    plan: varchar("plan", { length: 40 }).notNull().default("starter"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    emailIdx: index("waitlist_email_idx").on(t.email),
  }),
);

// ── Marketing: contact / book-a-call messages ───────────────────────
export const messages = pgTable("messages", {
  id: serial("id").primaryKey(),
  name: varchar("name", { length: 120 }).notNull(),
  email: varchar("email", { length: 200 }).notNull(),
  message: text("message").notNull(),
  createdAt: timestamp("created_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

// ── Auth: users ─────────────────────────────────────────────────────
export const users = pgTable(
  "users",
  {
    id: serial("id").primaryKey(),
    name: varchar("name", { length: 120 }).notNull(),
    email: varchar("email", { length: 200 }).notNull().unique(),
    passwordHash: text("password_hash").notNull(),
    role: varchar("role", { length: 20 }).notNull().default("user"),
    status: varchar("status", { length: 20 }).notNull().default("active"),
    // Cached mirror of the user's ledger cash account. The ledger is
    // authoritative; this column exists so reads stay a single-row lookup.
    balanceUsd: numeric("balance_usd", { precision: 18, scale: 2 })
      .notNull()
      .default("0"),
    referralCode: varchar("referral_code", { length: 16 }).notNull().unique(),
    referredById: integer("referred_by_id"),
    kycStatus: varchar("kyc_status", { length: 20 }).notNull().default("unverified"),
    // Operator control over how this account's simulated trades resolve:
    //   manual  — the desk must decide each trade (default)
    //   win     — the account's trades settle as wins
    //   loss    — the account's trades settle as losses
    //   market  — settle against the live price at expiry
    tradeOutcome: varchar("trade_outcome", { length: 10 })
      .notNull()
      .default("manual"),
    // Profile details collected on first sign-in. Null until the customer
    // finishes the onboarding form; `profileCompletedAt` is the gate.
    phone: varchar("phone", { length: 40 }),
    country: varchar("country", { length: 60 }),
    dateOfBirth: varchar("date_of_birth", { length: 10 }),
    addressLine: varchar("address_line", { length: 200 }),
    city: varchar("city", { length: 120 }),
    postalCode: varchar("postal_code", { length: 20 }),
    occupation: varchar("occupation", { length: 120 }),
    // none | beginner | intermediate | experienced
    tradingExperience: varchar("trading_experience", { length: 20 }),
    // grow_savings | retirement | short_term | learn | other
    investmentGoal: varchar("investment_goal", { length: 30 }),
    profileCompletedAt: timestamp("profile_completed_at", { withTimezone: true }),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    emailIdx: index("users_email_idx").on(t.email),
    referralIdx: uniqueIndex("users_referral_code_idx").on(t.referralCode),
  }),
);

// ── Portfolio: coin holdings per user ───────────────────────────────
export const holdings = pgTable(
  "holdings",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    coinId: varchar("coin_id", { length: 60 }).notNull(),
    symbol: varchar("symbol", { length: 20 }).notNull(),
    amount: numeric("amount", { precision: 28, scale: 10 })
      .notNull()
      .default("0"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("holdings_user_idx").on(t.userId),
  }),
);

// ── Ledger: one row per trade / cash movement shown in activity feeds ─
export const transactions = pgTable(
  "transactions",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    type: varchar("type", { length: 20 }).notNull(),
    coinId: varchar("coin_id", { length: 60 }),
    symbol: varchar("symbol", { length: 20 }),
    amount: numeric("amount", { precision: 28, scale: 10 }),
    price: numeric("price", { precision: 18, scale: 6 }),
    usdValue: numeric("usd_value", { precision: 18, scale: 2 }).notNull(),
    // Reason attached to operator adjustments; null for market activity.
    note: text("note"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("transactions_user_idx").on(t.userId),
  }),
);

// ── Simulated fixed-odds trades ─────────────────────────────────────
// A user stakes their own simulated cash on a direction. The stake leaves
// available cash and sits in escrow (`liability:user:<id>:stake`) as a user
// liability — it is still their money, it is just committed. An operator, or
// an explicit "settle at market" action, then decides the outcome. Nothing
// here mints money: a win is paid out of the house settlement expense account
// and a loss is recognised as house revenue, so both sides are journaled.
export const simTrades = pgTable(
  "sim_trades",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    coinId: varchar("coin_id", { length: 60 }).notNull(),
    symbol: varchar("symbol", { length: 20 }).notNull(),
    // long = price up wins, short = price down wins.
    direction: varchar("direction", { length: 6 }).notNull(),
    stakeUsd: numeric("stake_usd", { precision: 18, scale: 2 }).notNull(),
    entryPrice: numeric("entry_price", { precision: 18, scale: 6 }).notNull(),
    exitPrice: numeric("exit_price", { precision: 18, scale: 6 }),
    // Profit paid on a win, in basis points of the stake (8500 = 85%).
    payoutBp: integer("payout_bp").notNull().default(8500),
    durationMinutes: integer("duration_minutes").notNull(),
    // open | won | lost | void
    status: varchar("status", { length: 12 }).notNull().default("open"),
    // Who decided the outcome: operator | market.
    outcomeSource: varchar("outcome_source", { length: 12 }),
    payoutUsd: numeric("payout_usd", { precision: 18, scale: 2 }),
    settledBy: integer("settled_by"),
    settleNote: text("settle_note"),
    openedAt: timestamp("opened_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
    expiresAt: timestamp("expires_at", { withTimezone: true }).notNull(),
    settledAt: timestamp("settled_at", { withTimezone: true }),
  },
  (t) => ({
    userIdx: index("sim_trades_user_idx").on(t.userId),
    statusIdx: index("sim_trades_status_idx").on(t.status),
  }),
);

// ── Investment plans (simulated yields) ─────────────────────────────
export const plans = pgTable(
  "plans",
  {
    id: serial("id").primaryKey(),
    slug: varchar("slug", { length: 40 }).notNull().unique(),
    name: varchar("name", { length: 80 }).notNull(),
    tagline: varchar("tagline", { length: 200 }).notNull(),
    riskLevel: varchar("risk_level", { length: 20 }).notNull().default("balanced"),
    minAmountUsd: numeric("min_amount_usd", { precision: 18, scale: 2 }).notNull(),
    maxAmountUsd: numeric("max_amount_usd", { precision: 18, scale: 2 }).notNull(),
    // Simulated annualised rate expressed in basis points (100 bp = 1%).
    annualRateBp: integer("annual_rate_bp").notNull(),
    durationDays: integer("duration_days").notNull(),
    status: varchar("status", { length: 20 }).notNull().default("active"),
    sortOrder: integer("sort_order").notNull().default(0),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    slugIdx: uniqueIndex("plans_slug_idx").on(t.slug),
  }),
);

export const investments = pgTable(
  "investments",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    planId: integer("plan_id")
      .notNull()
      .references(() => plans.id),
    principalUsd: numeric("principal_usd", { precision: 18, scale: 2 }).notNull(),
    annualRateBp: integer("annual_rate_bp").notNull(),
    durationDays: integer("duration_days").notNull(),
    daysPaid: integer("days_paid").notNull().default(0),
    earnedUsd: numeric("earned_usd", { precision: 18, scale: 2 })
      .notNull()
      .default("0"),
    status: varchar("status", { length: 20 }).notNull().default("active"),
    startedAt: timestamp("started_at", { withTimezone: true }).notNull().defaultNow(),
    endsAt: timestamp("ends_at", { withTimezone: true }).notNull(),
    lastAccruedAt: timestamp("last_accrued_at", { withTimezone: true }),
  },
  (t) => ({
    userIdx: index("investments_user_idx").on(t.userId),
    statusIdx: index("investments_status_idx").on(t.status),
  }),
);

// ── Deposit / withdrawal requests with an admin review queue ────────
export const depositRequests = pgTable(
  "deposit_requests",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    amountUsd: numeric("amount_usd", { precision: 18, scale: 2 }).notNull(),
    method: varchar("method", { length: 30 }).notNull().default("bank_transfer"),
    reference: varchar("reference", { length: 80 }),
    status: varchar("status", { length: 20 }).notNull().default("pending"),
    adminNote: text("admin_note"),
    reviewedBy: integer("reviewed_by"),
    reviewedAt: timestamp("reviewed_at", { withTimezone: true }),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("deposit_requests_user_idx").on(t.userId),
    statusIdx: index("deposit_requests_status_idx").on(t.status),
  }),
);

export const withdrawalRequests = pgTable(
  "withdrawal_requests",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    amountUsd: numeric("amount_usd", { precision: 18, scale: 2 }).notNull(),
    destination: varchar("destination", { length: 160 }).notNull(),
    status: varchar("status", { length: 20 }).notNull().default("pending"),
    adminNote: text("admin_note"),
    reviewedBy: integer("reviewed_by"),
    reviewedAt: timestamp("reviewed_at", { withTimezone: true }),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("withdrawal_requests_user_idx").on(t.userId),
    statusIdx: index("withdrawal_requests_status_idx").on(t.status),
  }),
);

// ── Double-entry ledger ────────────────────────────────────────────
// Every balance change must post balanced entries. `normal` decides which
// side of the equation an account sits on:
//   debit  (assets, expenses)  — increases with +delta
//   credit (liabilities, revenue, equity) — increases with +delta
// Invariants enforced in src/lib/ledger.ts (before any write) and surfaced on
// the admin /admin/books screen:
//   Σ deltas = 0 per journal, with at least one debit and one credit.
//   Trial balance: Σ debit balances = Σ credit balances.
export const ledgerAccounts = pgTable(
  "ledger_accounts",
  {
    id: serial("id").primaryKey(),
    code: varchar("code", { length: 60 }).notNull().unique(),
    name: varchar("name", { length: 120 }).notNull(),
    kind: varchar("kind", { length: 20 }).notNull(), // asset | liability | revenue | expense | equity
    normal: varchar("normal", { length: 10 }).notNull(), // debit | credit
    ownerUserId: integer("owner_user_id"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    codeIdx: uniqueIndex("ledger_accounts_code_idx").on(t.code),
  }),
);

export const ledgerTransactions = pgTable("ledger_transactions", {
  id: serial("id").primaryKey(),
  kind: varchar("kind", { length: 30 }).notNull(),
  memo: varchar("memo", { length: 200 }).notNull(),
  refType: varchar("ref_type", { length: 30 }),
  refId: integer("ref_id"),
  actorId: integer("actor_id"),
  createdAt: timestamp("created_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

export const ledgerEntries = pgTable(
  "ledger_entries",
  {
    id: serial("id").primaryKey(),
    txnId: integer("txn_id")
      .notNull()
      .references(() => ledgerTransactions.id, { onDelete: "cascade" }),
    accountId: integer("account_id")
      .notNull()
      .references(() => ledgerAccounts.id),
    deltaUsd: numeric("delta_usd", { precision: 18, scale: 2 }).notNull(),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    txnIdx: index("ledger_entries_txn_idx").on(t.txnId),
    accountIdx: index("ledger_entries_account_idx").on(t.accountId),
  }),
);

// ── Notifications + audit trail ────────────────────────────────────
export const notifications = pgTable(
  "notifications",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    title: varchar("title", { length: 140 }).notNull(),
    body: text("body").notNull(),
    readAt: timestamp("read_at", { withTimezone: true }),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("notifications_user_idx").on(t.userId),
  }),
);

export const auditLog = pgTable(
  "audit_log",
  {
    id: serial("id").primaryKey(),
    actorId: integer("actor_id"),
    actorEmail: varchar("actor_email", { length: 200 }),
    action: varchar("action", { length: 60 }).notNull(),
    targetType: varchar("target_type", { length: 40 }),
    targetId: integer("target_id"),
    meta: text("meta"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    actionIdx: index("audit_log_action_idx").on(t.action),
  }),
);

// ── KYC submissions (metadata only — no document images stored) ─────
export const kycSubmissions = pgTable("kyc_submissions", {
  id: serial("id").primaryKey(),
  userId: integer("user_id")
    .notNull()
    .references(() => users.id, { onDelete: "cascade" })
    .unique(),
  fullName: varchar("full_name", { length: 140 }).notNull(),
  country: varchar("country", { length: 80 }).notNull(),
  documentType: varchar("document_type", { length: 40 }).notNull(),
  documentLast4: varchar("document_last4", { length: 4 }).notNull(),
  status: varchar("status", { length: 20 }).notNull().default("pending"),
  reviewNote: text("review_note"),
  reviewedBy: integer("reviewed_by"),
  reviewedAt: timestamp("reviewed_at", { withTimezone: true }),
  createdAt: timestamp("created_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

// ── CMS: editable website copy and images ──────────────────────────
// One row per editable slot. Missing keys fall back to the defaults declared
// in src/lib/content.ts, so a fresh database still renders the full site.
export const siteContent = pgTable("site_content", {
  key: varchar("key", { length: 80 }).primaryKey(),
  value: text("value").notNull(),
  updatedBy: integer("updated_by"),
  updatedAt: timestamp("updated_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

// Uploaded images (base64 payload + metadata) served from /api/media/[id].
export const mediaAssets = pgTable("media_assets", {
  id: serial("id").primaryKey(),
  filename: varchar("filename", { length: 160 }).notNull(),
  mime: varchar("mime", { length: 60 }).notNull(),
  sizeBytes: integer("size_bytes").notNull(),
  data: text("data").notNull(),
  uploadedBy: integer("uploaded_by"),
  createdAt: timestamp("created_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

export const featureFlags = pgTable("feature_flags", {
  key: varchar("key", { length: 60 }).primaryKey(),
  enabled: boolean("enabled").notNull().default(true),
  updatedAt: timestamp("updated_at", { withTimezone: true })
    .notNull()
    .defaultNow(),
});

export type Waitlist = typeof waitlist.$inferSelect;
export type NewWaitlist = typeof waitlist.$inferInsert;
export type Message = typeof messages.$inferSelect;
export type User = typeof users.$inferSelect;
export type Holding = typeof holdings.$inferSelect;
export type Transaction = typeof transactions.$inferSelect;
export type SimTrade = typeof simTrades.$inferSelect;
export type NewSimTrade = typeof simTrades.$inferInsert;
export type Plan = typeof plans.$inferSelect;
export type Investment = typeof investments.$inferSelect;
export type DepositRequest = typeof depositRequests.$inferSelect;
export type WithdrawalRequest = typeof withdrawalRequests.$inferSelect;
export type LedgerAccount = typeof ledgerAccounts.$inferSelect;
export type LedgerEntry = typeof ledgerEntries.$inferSelect;
export type Notification = typeof notifications.$inferSelect;
export type AuditEntry = typeof auditLog.$inferSelect;
export type KycSubmission = typeof kycSubmissions.$inferSelect;
export type SiteContentRow = typeof siteContent.$inferSelect;
export type MediaAsset = typeof mediaAssets.$inferSelect;
