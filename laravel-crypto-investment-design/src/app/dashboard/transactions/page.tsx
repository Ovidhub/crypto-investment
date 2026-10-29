import { requireUser } from "@/lib/auth";
import { getActivity } from "@/lib/portfolio";
import { formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

export default async function TransactionsPage() {
  const me = await requireUser();
  const activity = await getActivity(me.id, 100);

  const totalIn = activity
    .filter((a) => a.direction === "in")
    .reduce((sum, a) => sum + a.usd, 0);
  const totalOut = activity
    .filter((a) => a.direction === "out")
    .reduce((sum, a) => sum + a.usd, 0);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Activity"
        description="Every row below comes from a posted ledger transaction. Downloaded statements reconcile to the same entries."
      />

      <div className="grid gap-4 sm:grid-cols-3">
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Entries</p>
          <p className="mt-2 font-display text-2xl font-bold text-white">{activity.length}</p>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Credits</p>
          <p className="mt-2 font-display text-2xl font-bold text-accent">
            {formatUsd(totalIn)}
          </p>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Debits</p>
          <p className="mt-2 font-display text-2xl font-bold text-[#c7cedb]">
            {formatUsd(totalOut)}
          </p>
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {activity.length === 0 ? (
          <EmptyState
            title="No activity"
            body="Trades, deposits, withdrawals and plan accruals all appear here."
          />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[680px] text-sm">
              <thead className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                <tr>
                  <th className="px-6 py-3 font-semibold">Type</th>
                  <th className="px-6 py-3 font-semibold">Detail</th>
                  <th className="px-6 py-3 font-semibold">Amount</th>
                  <th className="px-6 py-3 font-semibold">Date</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {activity.map((a) => (
                  <tr key={`${a.type}-${a.id}`}>
                    <td className="px-6 py-4 font-semibold text-white">{a.label}</td>
                    <td className="px-6 py-4 text-xs text-[#9aa3b2]">{a.detail || "—"}</td>
                    <td
                      className={`px-6 py-4 tabular-nums ${
                        a.direction === "in" ? "text-accent" : "text-[#c7cedb]"
                      }`}
                    >
                      {a.direction === "in" ? "+" : a.direction === "out" ? "−" : ""}
                      {formatUsd(a.usd)}
                    </td>
                    <td className="px-6 py-4 text-xs text-[#7c8698]">
                      {new Date(a.createdAt).toLocaleString("en-US")}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
