import {
  pgTable,
  serial,
  text,
  varchar,
  timestamp,
  index,
  integer,
  numeric,
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
    // cash balance in USD available for trading
    balanceUsd: numeric("balance_usd", { precision: 18, scale: 2 })
      .notNull()
      .default("10000"),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    emailIdx: index("users_email_idx").on(t.email),
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
    // amount of the coin held
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

// ── Ledger: transactions (buy / sell / deposit / withdraw) ──────────
export const transactions = pgTable(
  "transactions",
  {
    id: serial("id").primaryKey(),
    userId: integer("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    type: varchar("type", { length: 20 }).notNull(), // buy | sell | deposit | withdraw
    coinId: varchar("coin_id", { length: 60 }),
    symbol: varchar("symbol", { length: 20 }),
    amount: numeric("amount", { precision: 28, scale: 10 }),
    price: numeric("price", { precision: 18, scale: 6 }),
    usdValue: numeric("usd_value", { precision: 18, scale: 2 }).notNull(),
    createdAt: timestamp("created_at", { withTimezone: true })
      .notNull()
      .defaultNow(),
  },
  (t) => ({
    userIdx: index("transactions_user_idx").on(t.userId),
  }),
);

export type Waitlist = typeof waitlist.$inferSelect;
export type NewWaitlist = typeof waitlist.$inferInsert;
export type Message = typeof messages.$inferSelect;
export type User = typeof users.$inferSelect;
export type Holding = typeof holdings.$inferSelect;
export type Transaction = typeof transactions.$inferSelect;
