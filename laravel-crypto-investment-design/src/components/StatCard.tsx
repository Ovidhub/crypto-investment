import type { ReactNode } from "react";

export default function StatCard({
  label,
  value,
  hint,
  tone = "default",
  children,
}: {
  label: string;
  value: ReactNode;
  hint?: ReactNode;
  tone?: "default" | "brand" | "accent" | "warn";
  children?: ReactNode;
}) {
  const shell =
    tone === "brand"
      ? "border-brand/30 bg-gradient-to-br from-brand/15 to-brand-2/5"
      : tone === "accent"
        ? "border-accent/25 bg-gradient-to-br from-accent/10 to-transparent"
        : tone === "warn"
          ? "border-[#f6465d]/30 bg-gradient-to-br from-[#f6465d]/10 to-transparent"
          : "bg-card";

  return (
    <div className={`rounded-2xl border border-line p-5 ${shell}`}>
      <p className="text-xs text-[#9aa3b2]">{label}</p>
      <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
        {value}
      </p>
      {hint && <div className="mt-1 text-xs text-[#7c8698]">{hint}</div>}
      {children}
    </div>
  );
}
