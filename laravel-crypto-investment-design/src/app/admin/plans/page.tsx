import { asc } from "drizzle-orm";
import { db } from "@/db";
import { plans } from "@/db/schema";
import { requireAdmin } from "@/lib/auth";
import PlanAdminPanel from "@/components/PlanAdminPanel";
import SectionHeader from "@/components/SectionHeader";

export const dynamic = "force-dynamic";

export default async function AdminPlansPage() {
  await requireAdmin();
  const rows = await db.select().from(plans).orderBy(asc(plans.sortOrder), asc(plans.id));

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Plan catalogue"
        description="Define the simulated products customers can fund."
      />
      <PlanAdminPanel
        plans={rows.map((p) => ({
          id: p.id,
          slug: p.slug,
          name: p.name,
          tagline: p.tagline,
          minAmountUsd: p.minAmountUsd,
          maxAmountUsd: p.maxAmountUsd,
          annualRateBp: p.annualRateBp,
          durationDays: p.durationDays,
          status: p.status,
        }))}
      />
    </div>
  );
}
