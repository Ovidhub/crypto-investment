import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { myInvestments } from "@/lib/plans";
import { formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import StatusPill from "@/components/StatusPill";
import EmptyState from "@/components/EmptyState";
import CloseInvestmentButton from "@/components/CloseInvestmentButton";

export const dynamic = "force-dynamic";

export default async function InvestmentsPage() {
  const me = await requireUser();
  const rows = await myInvestments(me.id);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="My plans"
        description="Active and closed plan positions. Accrual is credited once per day."
        action={
          <Link
            href="/dashboard/plans"
            className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
          >
            New plan
          </Link>
        }
      />

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {rows.length === 0 ? (
          <EmptyState
            title="No plan positions"
            body="Start a plan to see it tracked here with daily accrual and a release date."
            action="Browse plans"
            href="/dashboard/plans"
          />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[820px] text-sm">
              <thead className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                <tr>
                  <th className="px-6 py-3 font-semibold">Plan</th>
                  <th className="px-6 py-3 font-semibold">Principal</th>
                  <th className="px-6 py-3 font-semibold">Rate</th>
                  <th className="px-6 py-3 font-semibold">Progress</th>
                  <th className="px-6 py-3 font-semibold">Earned</th>
                  <th className="px-6 py-3 font-semibold">Status</th>
                  <th className="px-6 py-3" />
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {rows.map((i) => (
                  <tr key={i.id}>
                    <td className="px-6 py-4">
                      <p className="font-semibold text-white">{i.planName}</p>
                      <p className="text-[11px] text-[#7c8698]">
                        ends {new Date(i.endsAt).toLocaleDateString("en-US")}
                      </p>
                    </td>
                    <td className="px-6 py-4 tabular-nums text-[#c7cedb]">
                      {formatUsd(Number(i.principalUsd))}
                    </td>
                    <td className="px-6 py-4 tabular-nums text-[#c7cedb]">
                      {(i.annualRateBp / 100).toFixed(2)}%
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <div className="h-1.5 w-24 overflow-hidden rounded-full bg-panel-2">
                          <div
                            className="h-full rounded-full bg-gradient-to-r from-brand to-brand-2"
                            style={{
                              width: `${Math.min(100, Math.round((i.daysPaid / i.durationDays) * 100))}%`,
                            }}
                          />
                        </div>
                        <span className="text-[11px] tabular-nums text-[#7c8698]">
                          {i.daysPaid}/{i.durationDays}d
                        </span>
                      </div>
                    </td>
                    <td className="px-6 py-4 tabular-nums text-accent">
                      +{formatUsd(Number(i.earnedUsd))}
                    </td>
                    <td className="px-6 py-4">
                      <StatusPill status={i.status} />
                    </td>
                    <td className="px-6 py-4">
                      {i.status === "active" ? <CloseInvestmentButton id={i.id} /> : null}
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
