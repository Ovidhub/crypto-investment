import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { plansWithStats } from "@/lib/plans";
import { userBalances } from "@/lib/ledger";
import { centsToNumber } from "@/lib/money";
import { formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";
import StatCard from "@/components/StatCard";

export const dynamic = "force-dynamic";

export default async function PlansPage() {
  const me = await requireUser();
  const [plans, balances] = await Promise.all([plansWithStats(), userBalances(me.id)]);
  const cash = centsToNumber(balances.cash);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Investment plans"
        description="Each plan locks capital for a fixed term and credits a daily accrual. Rates are inputs to our model, not forecasts and not a promise."
      />

      <Notice tone="info" title="How this works">
        <p>
          The accrual job credits your wallet once a day while a plan is active,
          funded from the plan-yield expense line in the ledger. Nothing is
          traded on a real exchange and no return is guaranteed.
        </p>
      </Notice>

      <div className="grid gap-4 sm:grid-cols-3">
        <StatCard label="Available cash" value={formatUsd(cash)} />
        <StatCard label="Active catalogue" value={`${plans.length} plans`} />
        <StatCard
          label="Minimum ticket"
          value={formatUsd(
            plans.length ? Math.min(...plans.map((p) => Number(p.minUsd))) : 0,
          )}
        />
      </div>

      {plans.length === 0 ? (
        <p className="rounded-2xl border border-line bg-card p-8 text-center text-sm text-[#9aa3b2]">
          No plans are open right now.
        </p>
      ) : (
        <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          {plans.map((plan) => (
            <div
              key={plan.slug}
              className="flex flex-col rounded-2xl border border-line bg-card p-6"
            >
              <div className="flex items-start justify-between gap-2">
                <div>
                  <h2 className="font-display text-lg font-bold text-white">{plan.name}</h2>
                  <p className="mt-1 text-xs text-[#9aa3b2]">{plan.tagline}</p>
                </div>
                <span className="rounded-full bg-white/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#c7cedb]">
                  {plan.riskLevel}
                </span>
              </div>

              <p className="mt-5 font-display text-3xl font-bold text-white">
                {(plan.annualRateBp / 100).toFixed(2)}
                <span className="ml-1 text-sm font-medium text-[#9aa3b2]">% p.a. sim.</span>
              </p>
              <p className="text-xs text-[#7c8698]">over {plan.durationDays} days</p>

              <dl className="mt-5 space-y-2 text-xs">
                <div className="flex justify-between">
                  <dt className="text-[#7c8698]">Range</dt>
                  <dd className="tabular-nums text-[#c7cedb]">
                    ${Number(plan.minUsd).toLocaleString()} – $
                    {Number(plan.maxUsd).toLocaleString()}
                  </dd>
                </div>
                <div className="flex justify-between">
                  <dt className="text-[#7c8698]">Plans opened</dt>
                  <dd className="tabular-nums text-[#c7cedb]">{plan.subscribers}</dd>
                </div>
                <div className="flex justify-between">
                  <dt className="text-[#7c8698]">Principal</dt>
                  <dd className="tabular-nums text-[#c7cedb]">
                    {formatUsd(Number(plan.simulatedPrincipal))}
                  </dd>
                </div>
              </dl>

              <Link
                href={`/dashboard/plans/${plan.slug}`}
                className="mt-6 rounded-xl bg-gradient-to-r from-brand to-brand-2 px-4 py-2.5 text-center text-sm font-semibold text-white"
              >
                Configure
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
