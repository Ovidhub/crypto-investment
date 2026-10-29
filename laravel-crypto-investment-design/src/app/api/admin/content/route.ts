import { apiOwner } from "@/lib/auth";
import { saveSiteContent } from "@/lib/content";
import { logAudit } from "@/lib/audit";
import { rateLimit } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

/** Save website copy and image slots. Super admin only. */
export async function POST(req: Request) {
  const auth = await apiOwner();
  if (!auth.user) return auth.response;

  const limited = rateLimit(`content:${auth.user.id}`, 30, 60_000);
  if (!limited.ok) {
    return Response.json(
      { error: "Too many saves. Try again in a minute." },
      { status: 429 },
    );
  }

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }

  const values = (body as { values?: unknown })?.values;
  if (!values || typeof values !== "object") {
    return Response.json({ error: "No content supplied." }, { status: 400 });
  }

  const result = await saveSiteContent(
    values as Record<string, unknown>,
    auth.user.id,
  );
  if (!result.ok) {
    return Response.json(
      { error: result.error, fields: result.fields },
      { status: 400 },
    );
  }

  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: "content.update",
    targetType: "site_content",
    meta: { saved: result.saved },
  });

  return Response.json({
    ok: true,
    saved: result.saved,
    message:
      result.saved === 0
        ? "Saved. Fields left blank fall back to the default copy."
        : `Saved ${result.saved} ${result.saved === 1 ? "change" : "changes"}.`,
  });
}
