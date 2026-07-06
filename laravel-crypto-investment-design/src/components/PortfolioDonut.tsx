import type { PositionView } from "@/lib/portfolio";

export default function PortfolioDonut({
  positions,
  cash,
}: {
  positions: PositionView[];
  cash: number;
}) {
  const segments = [
    ...positions.map((p) => ({
      label: p.symbol,
      value: p.value,
      color: p.color,
    })),
    { label: "Cash", value: cash, color: "#22e58a" },
  ].filter((s) => s.value > 0);

  const total = segments.reduce((s, x) => s + x.value, 0) || 1;
  const R = 54;
  const C = 2 * Math.PI * R;
  let offset = 0;

  return (
    <div className="flex flex-col items-center gap-6 sm:flex-row">
      <svg viewBox="0 0 140 140" className="h-40 w-40 shrink-0 -rotate-90">
        <circle cx="70" cy="70" r={R} fill="none" stroke="#1e2530" strokeWidth="16" />
        {segments.map((s, i) => {
          const len = (s.value / total) * C;
          const el = (
            <circle
              key={i}
              cx="70"
              cy="70"
              r={R}
              fill="none"
              stroke={s.color}
              strokeWidth="16"
              strokeDasharray={`${len} ${C - len}`}
              strokeDashoffset={-offset}
            />
          );
          offset += len;
          return el;
        })}
      </svg>

      <div className="w-full space-y-2">
        {segments.map((s, i) => (
          <div key={i} className="flex items-center justify-between text-sm">
            <span className="flex items-center gap-2 text-[#c7cedb]">
              <span
                className="h-2.5 w-2.5 rounded-full"
                style={{ background: s.color }}
              />
              {s.label}
            </span>
            <span className="tabular-nums text-[#9aa3b2]">
              {((s.value / total) * 100).toFixed(1)}%
            </span>
          </div>
        ))}
      </div>
    </div>
  );
}
