import type { ReactNode } from "react";

export default function Notice({
  tone = "info",
  title,
  children,
}: {
  tone?: "info" | "warn" | "danger" | "success";
  title?: string;
  children: ReactNode;
}) {
  const styles = {
    info: "border-brand/25 bg-brand/5 text-[#c7cedb]",
    warn: "border-[#f5a524]/30 bg-[#f5a524]/5 text-[#e9d3a8]",
    danger: "border-[#f6465d]/30 bg-[#f6465d]/5 text-[#f0c2c7]",
    success: "border-accent/25 bg-accent/5 text-[#bfe9d4]",
  }[tone];

  return (
    <div className={`rounded-xl border px-4 py-3 text-sm ${styles}`}>
      {title && <p className="font-semibold">{title}</p>}
      <div className={title ? "mt-1" : ""}>{children}</div>
    </div>
  );
}
