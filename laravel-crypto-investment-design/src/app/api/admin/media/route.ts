import { apiOwner } from "@/lib/auth";
import { createMedia, MAX_UPLOAD_BYTES } from "@/lib/media";
import { logAudit } from "@/lib/audit";

export const dynamic = "force-dynamic";

/** Upload an image to the site media library. Super admin only. */
export async function POST(req: Request) {
  const auth = await apiOwner();
  if (!auth.user) return auth.response;

  const form = await req.formData().catch(() => null);
  const file = form?.get("file");
  if (!(file instanceof File)) {
    return Response.json({ error: "Choose an image to upload." }, { status: 400 });
  }
  if (file.size > MAX_UPLOAD_BYTES) {
    return Response.json(
      { error: "Images must be 2 MB or smaller." },
      { status: 400 },
    );
  }

  const bytes = Buffer.from(await file.arrayBuffer());
  const result = await createMedia(
    { filename: file.name, mime: file.type, bytes },
    auth.user.id,
  );
  if (!result.ok) {
    return Response.json({ error: result.error }, { status: 400 });
  }

  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: "media.upload",
    targetType: "media_asset",
    targetId: result.item.id,
    meta: { filename: result.item.filename, bytes: result.item.sizeBytes },
  });

  return Response.json({ ok: true, item: result.item });
}
