import Link from "next/link";
import { notFound } from "next/navigation";
import { desc, eq } from "drizzle-orm";
import { db } from "@/db";
import {
  depositRequests,
  holdings,
  investments,
  plans,
  transactions,
  users,
  withdrawalRequests,
} from "@/db/schema";
import { requireAdmin } from "@/lib/auth";
import { userBalances } from "@/lib/ledger";
import { formatUsd } from "@/lib/format";
import { centsToNumber, centsToUsd } from "@/lib/money";
import WalletAdjustmentForm from "@/components/WalletAdjustmentForm";
import SectionHeader from "@/components/SectionHeader";
import StatusPill from "@/components/StatusPill";
import StatCard from "@/components/StatCard";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminUserDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const me = await requireAdmin();
  const { id } = await params;
  const userId = Number(id);
  if (!Number.isInteger(userId)) notFound();

  const [user] = await db.select().from(users).where(eq(users.id, userId)).limit(1);
  if (!user) notFound();

  const [balances, positions, activity, deposits, withdrawals, plansInFlight] =
    await Promise.all([
      userBalances(userId),
      db.select().from(holdings).where(eq(holdings.userId, userId)),
      db
        .select()
        .from(transactions)
        .where(eq(transactions.userId, userId))
        .orderBy(desc(transactions.createdAt))
        .limit(15),
      db
        .select()
        .from(depositRequests)
        .where(eq(depositRequests.userId, userId))
        .orderBy(desc(depositRequests.createdAt))
        .limit(5),
      db
        .select()
        .from(withdrawalRequests)
        .where(eq(withdrawalRequests.userId, userId))
        .orderBy(desc(withdrawalRequests.createdAt))
        .limit(5),
      db
        .select({
          id: investments.id,
          principalUsd: investments.principalUsd,
          status: investments.status,
          daysPaid: investments.daysPaid,
          durationDays: investments.durationDays,
          earnedUsd: investments.earnedUsd,
          planName: plans.name,
        })
        .from(investments)
        .innerJoin(plans, eq(plans.id, investments.planId))
        .where(eq(investments.userId, userId))
        .orderBy(desc(investments.startedAt))
        .limit(10),
    ]);

  const cash = centsToUsd(balances.cash);

  return (
    <div className="space-y-8">
      <div>
        <Link href="/admin/users" className="text-xs font-semibold text-brand hover:underline">
          ← All users
        </Link>
        <div className="mt-3 flex flex-wrap items-center gap-3">
          <h1 className="font-display text-2xl font-bold sm:text-3xl">{user.name}</h1>
          <StatusPill status={user.status} />
          <StatusPill status={user.kycStatus} />
          <span className="rounded-full bg-brand/15 px-2.5 py-0.5 text-[11px] font-semibold capitalize text-brand">
            {user.role}
          </span>
        </div>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          {user.email} · referral code {user.referralCode} · joined{" "}
          {new Date(user.createdAt).toLocaleDateString("en-US")}
        </p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="Available cash" value={formatUsd(centsToNumber(balances.cash))} tone="brand" />
        <StatCard label="Crypto at cost" value={formatUsd(centsToNumber(balances.crypto))} />
        <StatCard label="Locked in plans" value={formatUsd(centsToNumber(balances.locked))} />
        <StatCard
          label="Positions"
          value={positions.length}
          hint={`${plansInFlight.filter((p) => p.status === "active").length} active plans`}
        />
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.15fr_1fr]">
        <div className="space-y-6">
          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Recent activity</h2>
            </div>
            {activity.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">No activity.</p>
            ) : (
              <div className="divide-y divide-line/60">
                {activity.map((t) => (
                  <div key={t.id} className="px-6 py-3.5">
                    <div className="flex items-center justify-between gap-3 text-sm">
                      <span className="font-semibold capitalize text-white">
                        {t.type.replace(/_/g, " ")}
                      </span>
                      <span className="tabular-nums text-[#c7cedb]">
                        {formatUsd(Number(t.usdValue))}
                      </span>
                    </div>
                    {t.note && <p className="mt-1 text-xs text-[#9aa3b2]">{t.note}</p>}
                    <p className="text-[11px] text-[#5d6675]">
                      {new Date(t.createdAt).toLocaleString("en-US")}
                    </p>
                  </div>
                ))}
              </div>
            )}
          </div>

          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Plan positions</h2>
            </div>
            {plansInFlight.length === 0 ? (
              <p className="p-6 text-sm text-[#9aa3b2]">No plans.</p>
            ) : (
              <div className="divide-y divide-line/60">
                {plansInFlight.map((p) => (
                  <div key={p.id} className="flex items-center justify-between px-6 py-3.5">
                    <div>
                      <p className="text-sm font-semibold text-white">{p.planName}</p>
                      <p className="text-[11px] text-[#7c8698]">
                        {formatUsd(Number(p.principalUsd))} · {p.daysPaid}/{p.durationDays} days
                      </p>
                    </div>
                    <div className="flex items-center gap-3">
                      <span className="text-xs tabular-nums text-accent">
                        +{formatUsd(Number(p.earnedUsd))}
                      </span>
                      <StatusPill status={p.status} />
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        <div className="space-y-4">
          {user.id === me.id ? (
            <Notice tone="warn" title="This is your own account">
              Wallet adjustments on yourself are blocked. Ask another admin to
              make the change so four-eyes review holds.
            </Notice>
          ) : (
            <WalletAdjustmentForm
              users={[]}
              fixedUser={{ id: user.id, name: user.name, cash }}
            />
          )}

          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Requests</h2>
            </div>
            <div className="divide-y divide-line/60">
              {[...deposits, ...withdrawals].length === 0 ? (
                <p className="p-6 text-sm text-[#9aa3b2]">No requests.</p>
              ) : (
                <>
                  {deposits.map((d) => (
                    <div key={`d-${d.id}`} className="flex items-center justify-between px-6 py-3 text-sm">
                      <span className="text-[#c7cedb]">Deposit</span>
                      <span className="tabular-nums text-white">{formatUsd(Number(d.amountUsd))}</span>
                      <StatusPill status={d.status} />
                    </div>
                  ))}
                  {withdrawals.map((w) => (
                    <div key={`w-${w.id}`} className="flex items-center justify-between px-6 py-3 text-sm">
                      <span className="text-[#c7cedb]">Withdrawal</span>
                      <span className="tabular-nums text-white">{formatUsd(Number(w.amountUsd))}</span>
                      <StatusPill status={w.status} />
                    </div>
                  ))}
                </>
              )}
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}
