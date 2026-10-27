import { desc, eq } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { requireUser } from "@/lib/auth";
import { formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import ReferralShare from "@/components/ReferralShare";
import Notice from "@/components/Notice";
import StatCard from "@/components/StatCard";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

export default async function ReferralsPage() {
  const me = await requireUser();
  const referees = await db
    .select({
      id: users.id,
      name: users.name,
      createdAt: users.createdAt,
      kycStatus: users.kycStatus,
    })
    .from(users)
    .where(eq(users.referredById, me.id))
    .orderBy(desc(users.createdAt))
    .limit(50);

  const bonus = 25;
  const earned = referees.length * bonus;

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Referrals"
        description="Share your code. Each successful signup credits a simulated bonus to your wallet and posts the matching expense in the ledger."
      />

      <div className="grid gap-6 lg:grid-cols-[1fr_1.4fr]">
        <div className="space-y-4">
          <ReferralShare code={me.referralCode} link={`/register?ref=${me.referralCode}`} />
          <div className="grid gap-4 sm:grid-cols-2">
            <StatCard label="Signups with your code" value={referees.length} />
            <StatCard
              label="Simulated bonuses"
              value={formatUsd(earned)}
              tone="accent"
            />
          </div>
          <Notice tone="warn" title="Anti-abuse">
            Bonuses are capped per account and reviewed. Self-referrals and bulk
            throwaway signups disqualify the reward.
          </Notice>
        </div>

        <div className="rounded-2xl border border-line bg-card">
          <div className="border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Your referrals</h2>
            <p className="text-xs text-[#7c8698]">Names are shown to you only.</p>
          </div>
          {referees.length === 0 ? (
            <EmptyState
              title="No referrals yet"
              body="Share the link above. The first signup credits a simulated bonus immediately."
            />
          ) : (
            <div className="divide-y divide-line/60">
              {referees.map((r) => (
                <div key={r.id} className="flex items-center justify-between px-6 py-4">
                  <div>
                    <p className="text-sm font-semibold text-white">{r.name}</p>
                    <p className="text-[11px] text-[#7c8698]">
                      joined {new Date(r.createdAt).toLocaleDateString("en-US")}
                    </p>
                  </div>
                  <span className="rounded-full bg-accent/15 px-2.5 py-0.5 text-[11px] font-semibold text-accent">
                    +{formatUsd(bonus)}
                  </span>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
