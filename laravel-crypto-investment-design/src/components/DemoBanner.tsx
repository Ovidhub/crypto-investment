import Link from "next/link";

/**
 * Persistent disclosure that every balance in this app is simulated. Rendered
 * on the marketing site and inside both dashboards so no screen can be
 * mistaken for a live financial service.
 */
export default function DemoBanner({ compact = false }: { compact?: boolean }) {
  if (compact) {
    return (
      <div className="flex items-center gap-2 border-b border-brand/30 bg-brand/10 px-4 py-1.5 text-[11px] font-semibold text-brand">
        <span className="inline-block h-1.5 w-1.5 rounded-full bg-brand" />
        Simulation — all balances are virtual money
      </div>
    );
  }
  return (
    <div className="border-b border-brand/30 bg-gradient-to-r from-brand/15 via-brand/5 to-transparent">
      <div className="mx-auto flex max-w-7xl flex-wrap items-center gap-x-3 gap-y-1 px-5 py-2.5 text-xs sm:px-8">
        <span className="rounded-full bg-brand/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand">
          Simulation
        </span>
        <p className="text-[#c7cedb]">
          Enethi is a product demo running on virtual funds. No real money is
          accepted, held, invested or paid out.
        </p>
        <Link href="/legal/risk" className="font-semibold text-brand hover:underline">
          Read the disclosure
        </Link>
      </div>
    </div>
  );
}
