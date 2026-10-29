const TONES: Record<string, string> = {
  active: "bg-accent/15 text-accent",
  approved: "bg-accent/15 text-accent",
  verified: "bg-accent/15 text-accent",
  completed: "bg-accent/15 text-accent",
  complete: "bg-accent/15 text-accent",
  paused: "bg-[#f5a524]/15 text-[#f5a524]",
  pending: "bg-[#f5a524]/15 text-[#f5a524]",
  unverified: "bg-white/10 text-[#9aa3b2]",
  suspended: "bg-[#f6465d]/15 text-[#f6465d]",
  rejected: "bg-[#f6465d]/15 text-[#f6465d]",
  cancelled: "bg-[#f6465d]/15 text-[#f6465d]",
};

export default function StatusPill({ status }: { status: string }) {
  const tone = TONES[status] ?? "bg-white/10 text-[#9aa3b2]";
  return (
    <span
      className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold capitalize ${tone}`}
    >
      {status.replace(/_/g, " ")}
    </span>
  );
}
