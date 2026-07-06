import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Faq from "@/components/Faq";
import { getCurrentUser } from "@/lib/auth";
import Link from "next/link";

export const dynamic = "force-dynamic";

const PLANS = [
  {
    name: "Starter",
    price: "$0",
    period: "/mo",
    desc: "For first-time investors getting started.",
    features: [
      "0.20% trading fee",
      "Buy & sell 99+ coins",
      "Basic portfolio tracking",
      "Email support",
    ],
    cta: "Get started",
    featured: false,
  },
  {
    name: "Pro",
    price: "$19",
    period: "/mo",
    desc: "For active traders who want an edge.",
    features: [
      "0.10% trading fee",
      "Advanced charting & signals",
      "Automated DCA & limit orders",
      "Up to 8% APY staking",
      "Priority 24/7 support",
    ],
    cta: "Start Pro trial",
    featured: true,
  },
  {
    name: "Whale",
    price: "$99",
    period: "/mo",
    desc: "Institutional-grade tools & limits.",
    features: [
      "0.04% trading fee",
      "Dedicated account manager",
      "OTC desk & deep liquidity",
      "Up to 12% APY yield vaults",
      "API access & white-glove onboarding",
    ],
    cta: "Contact sales",
    featured: false,
  },
];

export default async function PricingPage() {
  const me = await getCurrentUser();
  return (
    <>
      <Navbar loggedIn={!!me} />
      <main>
        <section className="relative overflow-hidden px-5 pb-10 pt-36 sm:px-8">
          <div className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 grid-bg" />
            <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
          </div>
          <div className="mx-auto max-w-2xl text-center">
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
              Pricing
            </span>
            <h1 className="mt-6 font-display text-[clamp(2.4rem,5vw,3.8rem)] font-bold leading-[1.02]">
              Simple, transparent{" "}
              <span className="brand-gradient">pricing</span>
            </h1>
            <p className="mt-6 text-base text-[#9aa3b2]">
              Start free and upgrade as you grow. No hidden fees, cancel anytime.
            </p>
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 py-14 sm:px-8">
          <div className="grid gap-6 lg:grid-cols-3">
            {PLANS.map((p) => (
              <div
                key={p.name}
                className={`relative flex flex-col rounded-3xl border p-8 ${
                  p.featured
                    ? "border-brand/50 bg-gradient-to-b from-brand/15 to-brand-2/5 glow-brand"
                    : "border-line bg-card"
                }`}
              >
                {p.featured && (
                  <span className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-brand to-brand-2 px-4 py-1 text-xs font-bold text-white">
                    Most popular
                  </span>
                )}
                <h3 className="font-display text-xl font-bold">{p.name}</h3>
                <p className="mt-2 text-sm text-[#9aa3b2]">{p.desc}</p>
                <div className="mt-6 flex items-end gap-1">
                  <span className="font-display text-4xl font-bold text-white">
                    {p.price}
                  </span>
                  <span className="mb-1 text-sm text-[#7c8698]">{p.period}</span>
                </div>
                <ul className="mt-6 flex-1 space-y-3">
                  {p.features.map((f) => (
                    <li key={f} className="flex items-center gap-3 text-sm text-[#c7cedb]">
                      <span className="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-accent/15 text-xs text-accent">
                        ✓
                      </span>
                      {f}
                    </li>
                  ))}
                </ul>
                <Link
                  href="/register"
                  className={`mt-8 rounded-xl py-3.5 text-center text-sm font-semibold transition-transform hover:scale-[1.02] ${
                    p.featured
                      ? "bg-gradient-to-r from-brand to-brand-2 text-white"
                      : "border border-line bg-panel-2 text-white"
                  }`}
                >
                  {p.cta}
                </Link>
              </div>
            ))}
          </div>
        </section>

        <Faq />
      </main>
      <Footer />
    </>
  );
}
