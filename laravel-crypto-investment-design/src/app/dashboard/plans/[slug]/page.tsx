import Link from "next/link";
import { notFound } from "next/navigation";
import { requireUser } from "@/lib/auth";
import { getPlanBySlug } from "@/lib/plans";
import { userBalances } from "@/lib/ledger";
import { centsToNumber } from "@/lib/money";
import { formatUsd } from "@/lib/format";
import PlanSubscribeForm from "@/components/PlanSubscribeForm";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function PlanDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const me = await requireUser();
  const plan = await getPlanBySlug(slug);
  if (!plan) notFound();

  const balances = await userBalances(me.id);
  const cash = centsToNumber(balances.cash);
  const daily = (Number(plan.minAmountUsd) * (plan.annualRateBp / 10000)) / 365;

  return (
    <div className="space-y-8">
      <div>
        <Link href="/dashboard/plans" className="text-xs font-semibold text-brand hover:underline">
          ← All plans
        </Link>
        <h1 className="mt-3 font-display text-2xl font-bold sm:text-3xl">{plan.name}</h1>
        <p className="mt-1 max-w-2xl text-sm text-[#9aa3b2]">{plan.tagline}</p>
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div className="space-y-6">
          <div className="grid gap-4 sm:grid-cols-3">
            <div className="rounded-2xl border border-line bg-card p-5">
              <p className="text-xs text-[#9aa3b2]">Simulated rate</p>
              <p className="mt-2 font-display text-2xl font-bold text-white">
                {(plan.annualRateBp / 100).toFixed(2)}%
              </p>
              <p className="text-[11px] text-[#7c8698]">annualised, not guaranteed</p>
            </div>
            <div className="rounded-2xl border border-line bg-card p-5">
              <p className="text-xs text-[#9aa3b2]">Term</p>
              <p className="mt-2 font-display text-2xl font-bold text-white">
                {plan.durationDays}d
              </p>
              <p className="text-[11px] text-[#7c8698]">principal returns at the end</p>
            </div>
            <div className="rounded-2xl border border-line bg-card p-5">
              <p className="text-xs text-[#9aa3b2]">Daily at the minimum</p>
              <p className="mt-2 font-display text-2xl font-bold text-white">
                {formatUsd(daily)}
              </p>
              <p className="text-[11px] text-[#7c8698]">simulated accrual</p>
            </div>
          </div>

          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="font-display font-bold">What happens when you start</h2>
            <ol className="mt-4 space-y-3 text-sm text-[#9aa3b2]">
              <li>
                <span className="font-semibold text-white">1. Principal is locked.</span>{" "}
                The amount moves from available cash to a locked liability in the
                ledger, so it can&apos;t be spent while the simulation runs.
              </li>
              <li>
                <span className="font-semibold text-white">2. Daily accrual posts.</span>{" "}
                A scheduled job credits the simulated daily rate to your wallet
                and books the matching expense.
              </li>
              <li>
                <span className="font-semibold text-white">3. Principal is released.</span>{" "}
                At the end of the term, the original amount returns to available
                cash. You can close early at any time.
              </li>
            </ol>
          </div>

          <Notice tone="danger" title="Risk disclosure">
            <p>
              Simulated figures are arbitrary model outputs. Past or simulated
              performance does not indicate future results, and this platform
              does not accept real deposits or pay real withdrawals.
            </p>
          </Notice>
        </div>

        <div className="space-y-4">
          <PlanSubscribeForm
            slug={plan.slug}
            name={plan.name}
            minUsd={plan.minAmountUsd}
            maxUsd={plan.maxAmountUsd}
            annualRateBp={plan.annualRateBp}
            durationDays={plan.durationDays}
            cash={cash}
          />
          <Link
            href="/dashboard/wallet"
            className="block rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
          >
            Add simulated funds
          </Link>
        </div>
      </div>
    </div>
  );
}
