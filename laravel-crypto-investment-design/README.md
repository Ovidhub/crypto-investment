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

No database is required to try it locally: without `DATABASE_URL` the app boots
an in-process Postgres (pg-mem) with a full seed.

> **On Vercel you must connect a database.** Serverless functions are separate
> processes, so the in-memory database is not shared between them: an account
> created by one request is invisible to the next, and sign-in fails with
> "Invalid email or password". Add one via **Storage → Neon → Connect to
> Project**; the app accepts `DATABASE_URL`, `POSTGRES_URL`,
> `POSTGRES_PRISMA_URL` or the `PG*` components, whichever the integration
> injects. It then creates its schema and seed data on first use (see
> `src/db/bootstrap.ts`) — no migration step needed.

### Reviewer account

The seed includes a funded customer so the product can be inspected without a
fresh signup:

```
amara@enethi.app
Enethi-Demo-2026!42
```

Override with `DEMO_LOGIN_EMAIL` / `DEMO_LOGIN_PASSWORD`. It is an ordinary
customer account (no admin rights) with a complete profile. It opens with
$24,562.50 cash, three coin positions, one settled win, one settled loss and one
live trade.

## Deploy

- Root directory: `laravel-crypto-investment-design`
- Framework: Next.js (auto-detected)
- Build: `npm run build` · Install: `npm install`

| Variable | Purpose |
| --- | --- |
| `AUTH_SECRET` | Signs session cookies. Long random value. |
| `ADMIN_EMAIL` | Email of the seeded admin account. |
| `ADMIN_PASSWORD_HASH` | scrypt hash of the admin password (`npm run env:generate`). |
| `DATABASE_URL` | **Required in production.** Any Postgres (Neon, Supabase, RDS). Tables and seed rows are created automatically on first query. Without it the database is in-memory and accounts do not persist between serverless invocations. |
| `CRON_SECRET` | Optional. When set, `/api/cron/accrue` requires `Authorization: Bearer <secret>`. |
| `DEMO_LOGIN_EMAIL` / `DEMO_LOGIN_PASSWORD` | Optional. Credentials of the seeded demo customer (demo mode only). |

`vercel.json` schedules the accrual job daily at 00:15 UTC.

With `DATABASE_URL` set, create the tables once with
`npx drizzle-kit push` (schema lives in `src/db/schema.ts`). Demo mode builds
its schema from the DDL in `src/db/demo.ts`, so nothing else is needed there.
Note that the shared demo login and its one-click endpoint are disabled as soon
as `DATABASE_URL` is present.

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
locked / trade-stake accounts, fee revenue, forfeited-stake revenue,
simulated-yield expense, winning-payout expense, referral-bonus expense,
operator-adjustment equity.
The admin **Ledger** screen shows the trial balance and the debits/credits
difference live.

### Product surfaces

| Area | Pages | Notes |
| --- | --- | --- |
| Marketing | `/`, `/pricing`, `/about`, `/contact`, `/legal/{risk,terms,privacy}` | Honest copy; pricing reads the live plan catalogue. |
| Account | `/login`, `/register` (+ `?ref=CODE`), `/onboarding`, `/suspended` | scrypt hashes, HMAC-signed HTTP-only cookie, login rate limiting, mandatory profile onboarding. |
| Portfolio | `/dashboard`, `/dashboard/portfolio`, `/dashboard/markets` | Live CoinGecko prices, cost-basis estimate, 0.50% fee quoted up front. |
| Trades | `/dashboard/trades` | Stake cash on a direction (5 min – 4 h). Stake is escrowed, then the desk settles it. |
| Wallet | `/dashboard/wallet` | Deposit/withdrawal **requests**; withdrawals reserve funds before review. |
| Plans | `/dashboard/plans`, `/dashboard/plans/[slug]`, `/dashboard/investments` | Locked principal, daily simulated accrual, early close. |
| Growth | `/dashboard/referrals`, `/dashboard/notifications`, `/dashboard/support` | $25 simulated referral bonus per signup, in-app notifications. |
| Account mgmt | `/dashboard/settings`, `/dashboard/kyc` | Password change, JSON data export, simulated verification. |
| Admin | `/admin`, `/admin/requests`, `/admin/kyc`, `/admin/books`, `/admin/users`, `/admin/users/[id]`, `/admin/trades`, `/admin/adjustments`, `/admin/plans`, `/admin/settings`, `/admin/audit`, `/admin/waitlist`, `/admin/messages` | KPIs, approvals, settlement desk, wallet adjustments, plan catalogue, feature flags, append-only audit trail. |

### Simulated yield

`GET /api/cron/accrue` credits each active plan once per calendar day:

```
daily = principal × annualRateBp ÷ 10 000 ÷ 365   (rounded down to the cent)
```

The credit is booked as `debit expense:simulated-yield / credit user cash`.
Re-running the job the same day is a no-op. At the end of the term the principal
moves back to available cash.

### Profile onboarding

A new account can sign in but cannot reach the dashboard until the onboarding
form is complete: phone, country, date of birth (18+), street address, city,
optional postal code and occupation, plus trading experience and stated goal.
`users.profile_completed_at` is the gate — `src/app/dashboard/layout.tsx`
redirects to `/onboarding` while it is null, and the API validates every field
server-side (`src/lib/profile.ts`).

- `POST /api/profile` saves the form (and is reused for later edits).
- `/dashboard/settings` shows the details with an **Edit** link to
  `/onboarding?edit=1`.
- `/admin/users/[id]` shows the customer's profile for the operator.
- Completion and later edits are written to the audit trail.

### Trade desk

A user stakes their **own** simulated cash on a direction (long or short) for a
fixed window. Opening a trade moves the stake from
`liability:user:X:cash` to `liability:user:X:stake` — escrow, not a loss.

Settlement is in the customer's hands only in the sense that they chose the
direction; the desk decides the outcome. Three honest paths, all journaled:

| Outcome | Journal | Customer sees |
| --- | --- | --- |
| won | debit stake, debit `expense:trade-payout`, credit cash | stake returned + 85% profit |
| lost | debit stake, credit `revenue:trade-settlement` | stake forfeited |
| void | debit stake, credit cash | stake returned untouched |

`settleSimTrade` takes `outcome: "won" | "lost" | "void" | "market"`. The last
one compares the live price with the entry price instead of asking an operator,
and is labelled on the trade as `settled at market` — an operator decision is
labelled `operator decision` with the actor id and a mandatory note. Because
losing stakes are recognised as house revenue and winning payouts as house
expense, the desk's P&L is visible on `/admin/books` instead of being hidden in
an equity balance.

#### Per-account outcome policy

An admin can also fix how **one account's** trades resolve, so a user never has
to be decided trade-by-trade. `users.trade_outcome` holds one of:

| Policy | Effect |
| --- | --- |
| `manual` (default) | Every trade waits on the desk. |
| `win` | Each trade pays out: stake returned plus the 85% profit. |
| `loss` | Each trade forfeits its stake to house revenue. |
| `market` | Each trade is judged on live price vs entry; a tie voids. |

Set it from `/admin/users/[id]` → **Trade outcome control**, with an optional
internal note (audited and shown to the customer in the notification) and an
"also settle the open trades now" tickbox. Under a non-manual policy, trades
settle automatically as soon as they pass their expiry — the runner
(`settleDueTrades`) executes when the customer's trade page, the desk or an
admin user page is loaded, so no scheduler is needed. Those settlements are
recorded with a null actor and `outcomeSource: "policy"`, and the customer's
receipt reads **house outcome policy** rather than pretending an operator or the
market made the call.

Endpoint: `POST /api/admin/trade-policy`
`{ userId, policy, reason?, applyNow? }` — admin only, and an admin cannot set a
policy on their own account.

Endpoints: `POST /api/trades` (user opens), `GET|POST /api/admin/trades`
(admin queue and settlement, admin role required).

### Roles

`user` → `support` → `admin` → `owner` (super admin).

| Role | Can do |
| --- | --- |
| `support` | Read queues, customers and the ledger. Cannot move money. |
| `admin` | Approvals, wallet adjustments, trade settlement, plans, users, flags. |
| `owner` | Everything above **plus** website content, the image library and team management. |

The last admin and the last super admin cannot be demoted or suspended. Every
privileged action writes to `audit_log`.

### Super admin: website content and images

`/admin/content` edits the live copy of the public site. Every editable string is
declared in `src/lib/content.ts` with a default value, grouped by page:

- brand name, page title, meta description, logo, support email
- home: hero, market strip, feature cards, insight block, steps, why-choose, CTA
- about, pricing and contact headlines
- footer about text and copyright

Saved values live in the `site_content` table; a blank field falls back to the
default, so nothing can be left broken. Headlines support `*word*` to render a
word in the brand gradient. Layout is fixed — this is a copy and image editor,
not a page builder.

`/admin/media` is the image library: upload a PNG/JPEG/WEBP/GIF/AVIF (max 2 MB),
copy its URL, and paste it into any image field. Uploads are stored in Postgres
and served from `/api/media/[id]`, so they survive redeploys (a Vercel function
filesystem is read-only).

`/admin/team` creates staff accounts, changes roles, issues temporary passwords
and suspends staff. Generated passwords are shown exactly once.

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
a visual page builder.

---

## Verification

```bash
npm run typecheck
npm run build
```

The flows are exercised end to end against a running server: sign in as the demo
user → open a simulated trade → settle it as an operator (won / lost / void /
market) → confirm the customer's activity feed, notification and CSV statement
match → confirm `/admin/books` still reports **Books are balanced**.

## Layout

```
src/lib/      money, ledger, accounts, wallet, trading, plans, portfolio, auth, audit, rate-limit
src/db/       schema (Drizzle), demo database + seed
src/app/      App Router pages and route handlers
src/components/ dashboard shell, panels, forms, tables
```
