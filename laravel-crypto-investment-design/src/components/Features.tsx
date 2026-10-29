const FEATURES = [
  {
    icon: "◈",
    title: "Trade on live prices",
    text: "Buy and sell the top coins at live market prices with integer-cent settlement. A 0.50% execution fee is booked to the platform ledger on every fill.",
  },
  {
    icon: "◷",
    title: "Plans",
    text: "Lock capital in a plan and watch a daily accrual post. Every credit is arithmetic on a rate you can inspect, never a promised return.",
  },
  {
    icon: "▤",
    title: "Books you can audit",
    text: "Every balance traces to a balanced journal entry. The admin ledger screen shows debits, credits and the difference in real time.",
  },
];

export default function Features() {
  return (
    <section id="features" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="mb-12 max-w-2xl">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
          What is actually built
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
          A full product surface, built on a real ledger
        </h2>
        <p className="mt-4 text-sm leading-relaxed text-[#9aa3b2]">
          Enethi is a complete crypto investment platform: accounts, wallets, a
          double-entry ledger, approval queues, plans, referrals, notifications
          and an admin console, all backed by one source of truth for money.
        </p>
      </div>

      <div className="grid gap-5 md:grid-cols-3">
        {FEATURES.map((f) => (
          <div
            key={f.title}
            className="group relative overflow-hidden rounded-3xl border border-line bg-card p-7 transition-all hover:-translate-y-1 hover:border-brand/40"
          >
            <div className="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand/10 blur-2xl transition-opacity group-hover:opacity-100 sm:opacity-0" />
            <div className="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-panel-2 text-2xl text-brand">
              {f.icon}
            </div>
            <h3 className="mt-5 font-display text-xl font-bold">{f.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">{f.text}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
