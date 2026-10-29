import { requireStaff } from "@/lib/auth";
import { kycQueue } from "@/lib/admin";
import KycReviewTable from "@/components/KycReviewTable";
import SectionHeader from "@/components/SectionHeader";

export const dynamic = "force-dynamic";

export default async function AdminKycPage() {
  await requireStaff();
  const rows = await kycQueue();

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Verification queue"
        description="Identity checks. Only the last four digits of a document are stored."
      />
      <div className="rounded-2xl border border-line bg-card">
        <KycReviewTable
          rows={rows.map((r) => ({
            id: r.id,
            userId: r.userId,
            fullName: r.fullName,
            country: r.country,
            documentType: r.documentType,
            documentLast4: r.documentLast4,
            status: r.status,
            createdAt: new Date(r.createdAt).toISOString(),
            userEmail: r.userEmail,
          }))}
        />
      </div>
    </div>
  );
}
