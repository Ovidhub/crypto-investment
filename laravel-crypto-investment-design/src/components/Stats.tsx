const STATS = [
  { value: "100%", label: "Simulated balances" },
  { value: "$0", label: "Real funds accepted" },
  { value: "Live", label: "Market prices" },
  { value: "2-sided", label: "Double-entry ledger" },
];

export default function Stats() {
  return (
    <section className="mx-auto max-w-7xl px-5 py-8 sm:px-8">
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {STATS.map((s) => (
          <div
            key={s.label}
            className="rounded-3xl border border-line bg-card p-7 text-center transition-colors hover:border-accent/40"
          >
            <p className="font-display text-[clamp(1.8rem,3vw,2.6rem)] font-bold text-accent">
              {s.value}
            </p>
            <p className="mt-1 text-sm text-[#9aa3b2]">{s.label}</p>
          </div>
        ))}
      </div>
      <p className="mt-4 text-center text-xs text-[#7c8698]">
        These are facts about how this demo is built, not traction metrics.
      </p>
    </section>
  );
}
