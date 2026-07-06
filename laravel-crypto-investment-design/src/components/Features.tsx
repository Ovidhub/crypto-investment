const FEATURES = [
  {
    icon: "💸",
    title: "Easy to sell & buy",
    text: "Convert between 99+ assets in seconds. Instant fiat on-ramps and off-ramps with zero hidden fees.",
  },
  {
    icon: "⚡",
    title: "Easy to trade",
    text: "One-tap trading, smart limit orders and automated DCA so you never miss a market move.",
  },
  {
    icon: "🛡️",
    title: "Bank-grade security",
    text: "Cold-storage custody, 2FA, biometric login and full insurance on your digital assets.",
  },
];

export default function Features() {
  return (
    <section id="features" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="mb-12 max-w-2xl">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
          Powerful features
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
          Everything you need to grow your crypto wealth
        </h2>
      </div>

      <div className="grid gap-5 md:grid-cols-3">
        {FEATURES.map((f) => (
          <div
            key={f.title}
            className="group relative overflow-hidden rounded-3xl border border-line bg-card p-7 transition-all hover:-translate-y-1 hover:border-brand/40"
          >
            <div className="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand/10 blur-2xl transition-opacity group-hover:opacity-100 sm:opacity-0" />
            <div className="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-panel-2 text-2xl">
              {f.icon}
            </div>
            <h3 className="mt-5 font-display text-xl font-bold">{f.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">
              {f.text}
            </p>
          </div>
        ))}
      </div>
    </section>
  );
}
