import { eq } from "drizzle-orm";
import { db } from "@/db";
import { kycSubmissions, users } from "@/db/schema";
import { apiAdmin } from "@/lib/auth";
import { logAudit, notify } from "@/lib/audit";

export const dynamic = "force-dynamic";

export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { id, decision, note } = (body ?? {}) as Record<string, unknown>;
  const submissionId = Number(id);
  if (!Number.isInteger(submissionId)) {
    return Response.json({ error: "Invalid submission." }, { status: 400 });
  }
  if (decision !== "approved" && decision !== "rejected") {
    return Response.json({ error: "Decision must be approved or rejected." }, { status: 400 });
  }

  const [submission] = await db
    .select()
    .from(kycSubmissions)
    .where(eq(kycSubmissions.id, submissionId))
    .limit(1);
  if (!submission) {
    return Response.json({ error: "Submission not found." }, { status: 404 });
  }

  await db
    .update(kycSubmissions)
    .set({
      status: decision,
      reviewNote: typeof note === "string" ? note.slice(0, 500) : null,
      reviewedBy: auth.user.id,
      reviewedAt: new Date(),
    })
    .where(eq(kycSubmissions.id, submissionId));
  await db
    .update(users)
    .set({ kycStatus: decision === "approved" ? "verified" : "unverified" })
    .where(eq(users.id, submission.userId));
  await notify({
    userId: submission.userId,
    title: decision === "approved" ? "Verification approved" : "Verification not approved",
    body:
      decision === "approved"
        ? "Your simulated identity check was approved."
        : `Please resubmit. ${typeof note === "string" ? note : ""}`.trim(),
  });
  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: `kyc.${decision}`,
    targetType: "kyc_submission",
    targetId: submissionId,
    meta: { userId: submission.userId },
  });

  return Response.json({ ok: true });
}
