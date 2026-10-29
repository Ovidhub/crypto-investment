import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Stats from "@/components/Stats";
import { getCurrentUser } from "@/lib/auth";
import Link from "next/link";

export const dynamic = "force-dynamic";

const VALUES = [
  {
    icon: "▤",
    title: "Booked, not invented",
    text: "Balances are derived from double-entry journals. A credit to a wallet always has a matching debit somewhere, and the trial balance is checked on the admin screen.",
  },
  {
    icon: "◉",
    title: "Labelled honestly",
    text: "No invented user counts, no unverifiable volume claims and no guaranteed-return language. Every rate we publish is a target, not a promise.",
  },
  {
    icon: "⚑",
    title: "Operator controls",
    text: "Deposits and withdrawals sit in an approval queue, withdrawals reserve funds before review, and every privileged action lands in an audit trail.",
  },
  {
    icon: "⌘",
    title: "Small, readable stack",
    text: "Next.js, Postgres via Drizzle, and a handful of focused modules for money, the ledger, trading and plans. Nothing hidden behind a vendor.",
  },
];

const STACK = [
  { label: "Framework", value: "Next.js App Router, React 19" },
  { label: "Data", value: "Postgres + Drizzle ORM" },
  { label: "Money", value: "Integer-cent arithmetic, no floats in balances" },
  { label: "Auth", value: "HMAC-signed HTTP-only cookies, scrypt hashes" },
  { label: "Prices", value: "CoinGecko public API, 60s cache, offline fallback" },
  { label: "Jobs", value: "/api/cron/accrue, protected by CRON_SECRET" },
];

const NOT_BUILT = [
  "Real payment rails or custody — nothing leaves the sandbox.",
  "Self-serve account deletion (support handles it so the audit trail stays intact).",
  "Two-factor authentication and device history.",
  "Automated document checks — verification is reviewed by our team.",
  "Copy-trading and bot-trading (planned, not shipped).",
];

export default async function AboutPage() {
  const me = await getCurrentUser();
  return (
    <>
      <Navbar loggedIn={!!me} />
      <main>
        <section className="relative overflow-hidden px-5 pb-16 pt-28 sm:px-8">
          <div className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 grid-bg" />
            <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
          </div>
          <div className="mx-auto max-w-3xl text-center">
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
              About this build
            </span>
            <h1 className="mt-6 font-display text-[clamp(2.4rem,5vw,3.8rem)] font-bold leading-[1.02]">
              A crypto platform{" "}
              <span className="brand-gradient">you can read the code of</span>
            </h1>
            <p className="mt-6 text-base leading-relaxed text-[#9aa3b2]">
              Enethi is a working reference implementation of a crypto
              investment product. Accounts, wallets, plans, approvals and the
              admin console are production code, built on a double-entry ledger
              and every screen says so.
            </p>
          </div>
        </section>

        <Stats />

        <section className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
          <div className="mb-12 max-w-2xl">
            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
              Design principles
            </p>
            <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
              What we optimised for
            </h2>
          </div>
          <div className="grid gap-5 sm:grid-cols-2">
            {VALUES.map((v) => (
              <div key={v.title} className="rounded-3xl border border-line bg-card p-7">
                <div className="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-panel-2 text-2xl text-brand">
                  {v.icon}
                </div>
                <h3 className="mt-5 font-display text-xl font-bold">{v.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">{v.text}</p>
              </div>
            ))}
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-20 sm:px-8">
          <div className="grid gap-6 lg:grid-cols-2">
            <div className="rounded-3xl border border-line bg-card p-7">
              <h2 className="font-display text-xl font-bold">Stack</h2>
              <dl className="mt-5 space-y-3">
                {STACK.map((s) => (
                  <div key={s.label} className="flex flex-wrap justify-between gap-2 border-b border-line/60 pb-3 last:border-0">
                    <dt className="text-xs font-semibold uppercase tracking-wider text-[#7c8698]">
                      {s.label}
                    </dt>
                    <dd className="text-right text-sm text-[#c7cedb]">{s.value}</dd>
                  </div>
                ))}
              </dl>
            </div>

            <div className="rounded-3xl border border-line bg-card p-7">
              <h2 className="font-display text-xl font-bold">Deliberately not built</h2>
              <p className="mt-2 text-sm text-[#9aa3b2]">
                We would rather say what is not built yet.
              </p>
              <ul className="mt-5 space-y-3">
                {NOT_BUILT.map((item) => (
                  <li key={item} className="flex gap-3 text-sm text-[#c7cedb]">
                    <span className="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-white/10 text-[11px] text-[#9aa3b2]">
                      –
                    </span>
                    {item}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-24 sm:px-8">
          <div className="rounded-[2rem] border border-line bg-gradient-to-br from-brand/15 to-accent/5 p-10 text-center">
            <h2 className="font-display text-[clamp(1.8rem,3.5vw,2.6rem)] font-bold">
              Try the whole workflow
            </h2>
            <p className="mx-auto mt-3 max-w-lg text-sm text-[#9aa3b2]">
              Create an account, get your opening balance, trade at live prices, fund
              a plan, request a withdrawal, then sign in as an admin and approve
              it. The books stay balanced the whole way.
            </p>
            <div className="mt-6 flex flex-wrap justify-center gap-3">
              <Link
                href="/register"
                className="rounded-full bg-gradient-to-r from-brand to-brand-2 px-7 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.03]"
              >
                Create an account
              </Link>
              <Link
                href="/legal/risk"
                className="rounded-full border border-line px-7 py-3.5 text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
              >
                Risk disclosure
              </Link>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </>
  );
}
