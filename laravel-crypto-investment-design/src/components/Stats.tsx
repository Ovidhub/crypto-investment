const STATS = [
  { value: "6M+", label: "Active users" },
  { value: "24/7", label: "User support" },
  { value: "160+", label: "Countries" },
  { value: "$22B+", label: "Trade volume" },
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
    </section>
  );
}
