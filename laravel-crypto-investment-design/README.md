# Enethi — Next.js front end

This directory is the Vercel-deployable Next.js version of the crypto
investment platform. The repository root also contains a Laravel/PHP
application; that application needs a PHP host and MySQL and is **not** what
this Vercel project deploys.

## Run locally

```bash
npm install
npm run env:generate        # prints AUTH_SECRET and admin credentials
cp .env.local.example .env.local
# paste the generated values into .env.local
npm run dev
```

Open http://localhost:3000. Admin sign-in is at `/login` (or `/admin/login`,
which redirects to `/login`).

## Deploy to Vercel

- Root directory: `laravel-crypto-investment-design`
- Framework: Next.js (auto-detected)
- Build command: `npm run build`
- Install command: `npm install`

Set these project environment variables for production, preview and
development:

| Variable | Purpose |
| --- | --- |
| `AUTH_SECRET` | Signs session cookies. Generate a long random value. |
| `ADMIN_EMAIL` | Email address of the seeded admin account. |
| `ADMIN_PASSWORD_HASH` | scrypt hash of the admin password. Generate with `npm run env:generate`. |
| `DATABASE_URL` | Optional. A Postgres connection string. |

Generate the values with:

```bash
npm run env:generate
```

Keep the printed admin password somewhere safe — only its hash is stored in
Vercel. To change the admin password later, run
`npm run env:generate -- "YourNewPassword"` and update
`ADMIN_PASSWORD_HASH`.

## Database modes

- **With `DATABASE_URL`**: the app uses Drizzle ORM + `pg` against a real
  Postgres database. Create the tables with `npx drizzle-kit push` using
  `drizzle.config.json` (point it at your database first).
- **Without `DATABASE_URL`**: the app runs in **demo mode** using `pg-mem`, an
  in-process Postgres emulator. It is seeded with clearly labelled demo users,
  transactions and waitlist entries. Data is **not persistent**: cold starts
  reset it, and separate serverless function instances do not share it. Use
  this mode for previews only.

The admin account is seeded from the environment variables in every function
instance, so admin sign-in works in demo mode. A banner in the admin overview
indicates when demo mode is active.

## Notes

- Market prices come from CoinGecko with a static fallback when the API is
  unreachable.
- Fonts are self-hosted through `@fontsource`, so builds do not depend on
  Google Fonts being reachable.
- `pg` and `pg-mem` are declared as Next.js server external packages.
