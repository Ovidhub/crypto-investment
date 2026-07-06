import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import Stats from "@/components/Stats";
import { getCurrentUser } from "@/lib/auth";
import Link from "next/link";

export const dynamic = "force-dynamic";

const VALUES = [
  {
    icon: "🔒",
    title: "Security first",
    text: "Cold-storage custody, full insurance and continuous audits keep your assets protected around the clock.",
  },
  {
    icon: "🌍",
    title: "Access for everyone",
    text: "We believe crypto investing should be simple. Enethi works in 160+ countries with no jargon required.",
  },
  {
    icon: "📈",
    title: "Built to grow wealth",
    text: "Automated staking, yield vaults and smart tools help your money compound over the long term.",
  },
  {
    icon: "⚡",
    title: "Speed & reliability",
    text: "A matching engine that executes in milliseconds with 99.99% uptime you can count on.",
  },
];

const TEAM = [
  { name: "Ava Chen", role: "CEO & Co-founder", color: "#b57bff" },
  { name: "Marcus Reid", role: "CTO & Co-founder", color: "#22e58a" },
  { name: "Sofia Alvarez", role: "Head of Security", color: "#627eea" },
  { name: "Daniel Kim", role: "Head of Product", color: "#f7931a" },
];

export default async function AboutPage() {
  const me = await getCurrentUser();
  return (
    <>
      <Navbar loggedIn={!!me} />
      <main>
        <section className="relative overflow-hidden px-5 pb-16 pt-36 sm:px-8">
          <div className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 grid-bg" />
            <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
          </div>
          <div className="mx-auto max-w-3xl text-center">
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
              About Enethi
            </span>
            <h1 className="mt-6 font-display text-[clamp(2.4rem,5vw,3.8rem)] font-bold leading-[1.02]">
              We&apos;re making crypto investing{" "}
              <span className="brand-gradient">simple for everyone</span>
            </h1>
            <p className="mt-6 text-base leading-relaxed text-[#9aa3b2]">
              Founded in 2021, Enethi is on a mission to open the door to digital
              wealth. We combine bank-grade security with an experience anyone
              can use — from first-time buyers to seasoned traders.
            </p>
          </div>
        </section>

        <Stats />

        <section className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
          <div className="mb-12 max-w-2xl">
            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
              Our values
            </p>
            <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
              What drives us every day
            </h2>
          </div>
          <div className="grid gap-5 sm:grid-cols-2">
            {VALUES.map((v) => (
              <div
                key={v.title}
                className="rounded-3xl border border-line bg-card p-7"
              >
                <div className="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-panel-2 text-2xl">
                  {v.icon}
                </div>
                <h3 className="mt-5 font-display text-xl font-bold">{v.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">
                  {v.text}
                </p>
              </div>
            ))}
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-20 sm:px-8">
          <div className="mb-12 text-center">
            <p className="text-sm font-semibold uppercase tracking-[0.18em] text-accent">
              Leadership
            </p>
            <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
              Meet the team
            </h2>
          </div>
          <div className="grid grid-cols-2 gap-5 lg:grid-cols-4">
            {TEAM.map((t) => (
              <div
                key={t.name}
                className="rounded-3xl border border-line bg-card p-6 text-center"
              >
                <span
                  className="mx-auto grid h-16 w-16 place-items-center rounded-full font-display text-xl font-bold text-ink"
                  style={{ background: `linear-gradient(135deg, ${t.color}, ${t.color}aa)` }}
                >
                  {t.name.split(" ").map((n) => n[0]).join("")}
                </span>
                <p className="mt-4 font-semibold text-white">{t.name}</p>
                <p className="text-xs text-[#7c8698]">{t.role}</p>
              </div>
            ))}
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-24 sm:px-8">
          <div className="rounded-[2rem] border border-line bg-gradient-to-br from-brand/15 to-accent/5 p-10 text-center">
            <h2 className="font-display text-[clamp(1.8rem,3.5vw,2.6rem)] font-bold">
              Ready to start your journey?
            </h2>
            <p className="mx-auto mt-3 max-w-md text-sm text-[#9aa3b2]">
              Join thousands of investors growing their wealth with Enethi.
            </p>
            <Link
              href="/register"
              className="mt-6 inline-block rounded-full bg-gradient-to-r from-brand to-brand-2 px-7 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.03]"
            >
              Create free account
            </Link>
          </div>
        </section>
      </main>
      <Footer />
    </>
  );
}
