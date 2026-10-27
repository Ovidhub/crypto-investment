# Enethi — crypto investment platform (simulated)

A complete, working crypto-investment product: accounts, wallets, a double-entry
ledger, deposit/withdrawal approval queues, investment plans with a daily accrual
job, trading at live prices, referrals, a simulated verification workflow,
notifications and an admin console.

**Everything runs on simulated money.** There is no payment processor, no
custody, no exchange order routing and no blockchain transaction anywhere in the
app. Every screen labelled "simulated" means exactly that.

This directory is the Vercel-deployable Next.js application. The repository root
also contains an older Laravel/PHP application that needs PHP + MySQL; that one
is kept as a feature reference and is **not** what this Vercel project deploys.

---

## Quick start

```bash
npm install
npm run env:generate       # prints AUTH_SECRET + admin email/password/hash
cp .env.local.example .env.local   # paste the generated values
npm run dev                # http://localhost:3000
```

No database is required to try it: without `DATABASE_URL` the app boots an
in-process Postgres (pg-mem) with a full seed. That data lives in the server
process and resets on restart.

## Deploy

- Root directory: `laravel-crypto-investment-design`
- Framework: Next.js (auto-detected)
- Build: `npm run build` · Install: `npm install`

| Variable | Purpose |
| --- | --- |
| `AUTH_SECRET` | Signs session cookies. Long random value. |
| `ADMIN_EMAIL` | Email of the seeded admin account. |
| `ADMIN_PASSWORD_HASH` | scrypt hash of the admin password (`npm run env:generate`). |
| `DATABASE_URL` | Optional Postgres URL. Without it the demo DB is in-memory. |
| `CRON_SECRET` | Optional. When set, `/api/cron/accrue` requires `Authorization: Bearer <secret>`. |

`vercel.json` schedules the accrual job daily at 00:15 UTC.

---

## What's implemented

### Money

- `src/lib/money.ts` — integer **cent** arithmetic (`bigint`), basis points,
  strict input parsing. No floating-point balances anywhere.
- `src/lib/ledger.ts` — double-entry core.

### The ledger

Every balance change posts a **balanced journal**:

```ts
await postJournal({
  kind: "trade.fee",
  memo: "Execution fee on BTC buy",
  postings: [
    debit(ACCOUNTS.userCash(userId), fee),      // liability down
    credit(ACCOUNTS.feeRevenue, fee),           // revenue up
  ],
});
```

Conventions:

- Postings are stored **debit-positive** (debit `+x`, credit `−x`), so the
  invariant is simply `Σ deltas = 0`.
- `debit()` / `credit()` reject non-positive amounts; `assertBalanced()` rejects
  unbalanced journals, zero legs and single-sided journals *before* any write.
- An account's natural balance flips the sign for credit-normal accounts.
- On a real Postgres connection each journal runs inside a transaction.

Chart of accounts: custody cash, crypto held, per-user cash / crypto-at-cost /
locked accounts, fee revenue, simulated-yield expense, referral-bonus expense.
The admin **Ledger** screen shows the trial balance and the debits/credits
difference live.

### Product surfaces

| Area | Pages | Notes |
| --- | --- | --- |
| Marketing | `/`, `/pricing`, `/about`, `/contact`, `/legal/{risk,terms,privacy}` | Honest copy; pricing reads the live plan catalogue. |
| Account | `/login`, `/register` (+ `?ref=CODE`), `/suspended` | scrypt hashes, HMAC-signed HTTP-only cookie, login rate limiting. |
| Portfolio | `/dashboard`, `/dashboard/portfolio`, `/dashboard/markets` | Live CoinGecko prices, cost-basis estimate, 0.50% fee quoted up front. |
| Wallet | `/dashboard/wallet` | Deposit/withdrawal **requests**; withdrawals reserve funds before review. |
| Plans | `/dashboard/plans`, `/dashboard/plans/[slug]`, `/dashboard/investments` | Locked principal, daily simulated accrual, early close. |
| Growth | `/dashboard/referrals`, `/dashboard/notifications`, `/dashboard/support` | $25 simulated referral bonus per signup, in-app notifications. |
| Account mgmt | `/dashboard/settings`, `/dashboard/kyc` | Password change, JSON data export, simulated verification. |
| Admin | `/admin`, `/admin/requests`, `/admin/kyc`, `/admin/books`, `/admin/users`, `/admin/plans`, `/admin/settings`, `/admin/audit`, `/admin/waitlist`, `/admin/messages` | KPIs, approvals, plan catalogue, feature flags, append-only audit trail. |

### Simulated yield

`GET /api/cron/accrue` credits each active plan once per calendar day:

```
daily = principal × annualRateBp ÷ 10 000 ÷ 365   (rounded down to the cent)
```

The credit is booked as `debit expense:simulated-yield / credit user cash`.
Re-running the job the same day is a no-op. At the end of the term the principal
moves back to available cash.

### Roles

`user` → `support` → `admin`. Support can read queues; only admins can approve,
change roles or flip flags. The last admin cannot be demoted. Every privileged
action writes to `audit_log`.

---

## Deliberate non-features

These are refusals, not omissions:

- **No real deposit rails feeding fixed returns.** Approving a deposit credits
  *simulated* funds; no card, bank or chain is connected.
- **No "credit balance as profit" button.** Money enters through an approved
  deposit request, plan accrual or trade settlement — each with a journal.
- **No fabricated social proof.** No invented user counts, volume figures,
  testimonials, team members or APY promises.
- **No guaranteed-return language.** Plan rates are model inputs, and the UI says
  so on every screen that shows one.

Still to build: 2FA, device/session history, email verification, self-serve
account deletion, copy-trading and bot-trading simulators, support ticketing,
CMS-driven blog.

---

## Verification

```bash
npm run typecheck
npm run build
```

The flows are exercised end to end against a running server: register → trade →
fund a plan → request a withdrawal → run accrual → approve as admin → confirm
`/admin/books` still reports **Books are balanced**.

## Layout

```
src/lib/      money, ledger, accounts, wallet, trading, plans, portfolio, auth, audit, rate-limit
src/db/       schema (Drizzle), demo database + seed
src/app/      App Router pages and route handlers
src/components/ dashboard shell, panels, forms, tables
```
