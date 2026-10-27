import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Faq from "@/components/Faq";
import DemoBanner from "@/components/DemoBanner";
import { getCurrentUser } from "@/lib/auth";
import { plansWithStats } from "@/lib/plans";
import Link from "next/link";

export const dynamic = "force-dynamic";

export default async function PricingPage() {
  const me = await getCurrentUser();
  const plans = await plansWithStats();

  return (
    <>
      <DemoBanner />
      <Navbar loggedIn={!!me} />
      <main>
        <section className="relative overflow-hidden px-5 pb-10 pt-28 sm:px-8">
          <div className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 grid-bg" />
            <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
          </div>
          <div className="mx-auto max-w-2xl text-center">
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
              Simulated products
            </span>
            <h1 className="mt-6 font-display text-[clamp(2.4rem,5vw,3.8rem)] font-bold leading-[1.02]">
              Plans, with the{" "}
              <span className="brand-gradient">maths shown</span>
            </h1>
            <p className="mt-6 text-base text-[#9aa3b2]">
              No subscription is charged and no card is collected. These are the
              simulated products in the demo catalogue, read live from the
              database.
            </p>
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 py-14 sm:px-8">
          {plans.length === 0 ? (
            <p className="rounded-3xl border border-line bg-card p-10 text-center text-sm text-[#9aa3b2]">
              The catalogue is empty right now.
            </p>
          ) : (
            <div className="grid gap-6 lg:grid-cols-3">
              {plans.map((p, index) => {
                const featured = index === 1;
                const daily = (1000 * (p.annualRateBp / 10000)) / 365;
                return (
                  <div
                    key={p.slug}
                    className={`relative flex flex-col rounded-3xl border p-8 ${
                      featured
                        ? "border-brand/50 bg-gradient-to-b from-brand/15 to-brand-2/5 glow-brand"
                        : "border-line bg-card"
                    }`}
                  >
                    {featured && (
                      <span className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-brand to-brand-2 px-4 py-1 text-xs font-bold text-white">
                        Simulated rate
                      </span>
                    )}
                    <h2 className="font-display text-xl font-bold">{p.name}</h2>
                    <p className="mt-2 text-sm text-[#9aa3b2]">{p.tagline}</p>

                    <div className="mt-6 flex items-end gap-1">
                      <span className="font-display text-4xl font-bold text-white">
                        {(p.annualRateBp / 100).toFixed(2)}%
                      </span>
                      <span className="mb-1 text-sm text-[#7c8698]">p.a. sim.</span>
                    </div>
                    <p className="mt-1 text-xs text-[#7c8698]">
                      {p.durationDays}-day term · ${Number(p.minUsd).toLocaleString()}–
                      ${Number(p.maxUsd).toLocaleString()}
                    </p>

                    <ul className="mt-6 flex-1 space-y-3">
                      {[
                        `~$${daily.toFixed(2)}/day on a $1,000 position`,
                        "Principal released at the end of the term",
                        "Close early at any time",
                        `${p.subscribers} simulation${p.subscribers === 1 ? "" : "s"} opened so far`,
                      ].map((f) => (
                        <li key={f} className="flex items-center gap-3 text-sm text-[#c7cedb]">
                          <span className="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-xs text-accent">
                            ✓
                          </span>
                          {f}
                        </li>
                      ))}
                    </ul>

                    <Link
                      href={me ? `/dashboard/plans/${p.slug}` : "/register"}
                      className={`mt-8 rounded-xl py-3.5 text-center text-sm font-semibold transition-transform hover:scale-[1.02] ${
                        featured
                          ? "bg-gradient-to-r from-brand to-brand-2 text-white"
                          : "border border-line bg-panel-2 text-white"
                      }`}
                    >
                      {me ? "Configure plan" : "Create a demo account"}
                    </Link>
                  </div>
                );
              })}
            </div>
          )}

          <div className="mt-10 rounded-3xl border border-line bg-panel-2 p-6 text-sm text-[#9aa3b2]">
            <p className="font-semibold text-white">What the rate means</p>
            <p className="mt-2">
              The daily credit is principal × annual rate ÷ 365, rounded down to
              the cent. It is produced by our own simulator, booked as an expense
              in the ledger, and can be changed or stopped by an operator. It is
              not a market yield, a fund return, or a promise. Crypto markets can
              fall as well as rise, and nothing here is financial advice.
            </p>
            <Link
              href="/legal/risk"
              className="mt-3 inline-block text-xs font-semibold text-brand hover:underline"
            >
              Read the full risk disclosure →
            </Link>
          </div>
        </section>

        <Faq />
      </main>
      <Footer />
    </>
  );
}
