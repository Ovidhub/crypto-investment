import { eq } from "drizzle-orm";
import { db } from "@/db";
import { kycSubmissions } from "@/db/schema";
import { requireUser } from "@/lib/auth";
import KycForm from "@/components/KycForm";
import SectionHeader from "@/components/SectionHeader";
import StatusPill from "@/components/StatusPill";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function KycPage() {
  const me = await requireUser();
  const [submission] = await db
    .select()
    .from(kycSubmissions)
    .where(eq(kycSubmissions.userId, me.id))
    .limit(1);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Verification"
        description="Submit your identity details for review. Only the last four digits of your document number are stored."
        action={<StatusPill status={me.kycStatus} />}
      />

      <Notice tone="warn" title="Do not enter real documents">
        We store only the last four digits of whatever you type and never
        uploads an image. Enter fake data so no real identity document leaves
        your device.
      </Notice>

      <div className="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <KycForm currentStatus={me.kycStatus} />

        <div className="space-y-4">
          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="font-display font-bold">Submission</h2>
            {submission ? (
              <dl className="mt-4 space-y-2 text-sm">
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Name</dt>
                  <dd className="text-right text-[#c7cedb]">{submission.fullName}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Country</dt>
                  <dd className="text-right text-[#c7cedb]">{submission.country}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Document</dt>
                  <dd className="text-right text-[#c7cedb]">
                    {submission.documentType} ····{submission.documentLast4}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Status</dt>
                  <dd className="text-right">
                    <StatusPill status={submission.status} />
                  </dd>
                </div>
                {submission.reviewNote && (
                  <div className="rounded-lg bg-panel-2 px-3 py-2 text-xs text-[#c7cedb]">
                    Reviewer: {submission.reviewNote}
                  </div>
                )}
              </dl>
            ) : (
              <p className="mt-3 text-sm text-[#9aa3b2]">
                Nothing submitted yet. Your account works fully without it —
                verification unlocks the verified badge on your account.
              </p>
            )}
          </div>

          <div className="rounded-2xl border border-line bg-panel-2 p-5 text-xs text-[#7c8698]">
            Access control: only admins can approve or reject a submission. Every
            decision is written to the audit log with the reviewer&apos;s identity.
          </div>
        </div>
      </div>
    </div>
  );
}
