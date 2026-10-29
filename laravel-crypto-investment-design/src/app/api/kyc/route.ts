import { eq } from "drizzle-orm";
import { db } from "@/db";
import { kycSubmissions, users } from "@/db/schema";
import { apiUser } from "@/lib/auth";
import { logAudit, notify } from "@/lib/audit";
import { last4 } from "@/lib/ids";

export const dynamic = "force-dynamic";

/**
 * Simulated KYC. Only non-sensitive metadata is stored (name, country,
 * document type, last four digits). No document images are uploaded, and the
 * copy in the UI says so explicitly — this is a workflow demo, not identity
 * verification.
 */
export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { fullName, country, documentType, documentNumber } = (body ?? {}) as Record<
    string,
    unknown
  >;

  if (typeof fullName !== "string" || fullName.trim().length < 2) {
    return Response.json({ error: "Enter your full legal name." }, { status: 400 });
  }
  if (typeof country !== "string" || country.trim().length < 2) {
    return Response.json({ error: "Select a country." }, { status: 400 });
  }
  if (typeof documentType !== "string" || documentType.trim().length < 2) {
    return Response.json({ error: "Select a document type." }, { status: 400 });
  }
  if (typeof documentNumber !== "string" || documentNumber.replace(/\D/g, "").length < 4) {
    return Response.json(
      { error: "Enter at least the last four digits of the document." },
      { status: 400 },
    );
  }

  const existing = await db
    .select({ id: kycSubmissions.id, status: kycSubmissions.status })
    .from(kycSubmissions)
    .where(eq(kycSubmissions.userId, auth.user.id))
    .limit(1);

  if (existing[0]?.status === "approved") {
    return Response.json(
      { error: "Your identity check is already approved." },
      { status: 409 },
    );
  }

  const values = {
    userId: auth.user.id,
    fullName: fullName.trim().slice(0, 140),
    country: country.trim().slice(0, 80),
    documentType: documentType.trim().slice(0, 40),
    documentLast4: last4(documentNumber),
    status: "pending" as const,
    reviewNote: null,
    reviewedBy: null,
    reviewedAt: null,
  };

  if (existing.length) {
    await db.delete(kycSubmissions).where(eq(kycSubmissions.userId, auth.user.id));
  }
  await db.insert(kycSubmissions).values(values);
  await db
    .update(users)
    .set({ kycStatus: "pending" })
    .where(eq(users.id, auth.user.id));

  await notify({
    userId: auth.user.id,
    title: "Verification submitted",
    body: "Your identity check is in the review queue.",
  });
  await logAudit({
    actorId: auth.user.id,
    action: "kyc.submit",
    targetType: "user",
    targetId: auth.user.id,
  });

  return Response.json({ ok: true });
}
