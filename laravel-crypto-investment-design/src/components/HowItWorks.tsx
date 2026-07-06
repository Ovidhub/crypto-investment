const STEPS = [
  {
    step: "01",
    title: "Create your account",
    text: "Sign up in under two minutes and verify your identity securely.",
  },
  {
    step: "02",
    title: "Fund your wallet",
    text: "Deposit with card, bank transfer or crypto — instant and fee-free.",
  },
  {
    step: "03",
    title: "Start earning",
    text: "Buy, trade or stake and watch your passive income grow daily.",
  },
];

export default function HowItWorks() {
  return (
    <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8">
      <div className="mb-10 text-center">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-accent">
          Simple process
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
          Start earning in 3 easy steps
        </h2>
      </div>

      <div className="grid gap-5 md:grid-cols-3">
        {STEPS.map((s) => (
          <div
            key={s.step}
            className="relative overflow-hidden rounded-3xl border border-line bg-card p-8"
          >
            <span className="font-display text-6xl font-bold text-white/5">
              {s.step}
            </span>
            <h3 className="mt-2 font-display text-xl font-bold">{s.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">
              {s.text}
            </p>
          </div>
        ))}
      </div>
    </section>
  );
}
