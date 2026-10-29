import { apiOwner } from "@/lib/auth";
import { deleteMedia } from "@/lib/media";
import { logAudit } from "@/lib/audit";

export const dynamic = "force-dynamic";

/** Remove an image from the library. Super admin only. */
export async function DELETE(
  _req: Request,
  { params }: { params: Promise<{ id: string }> },
) {
  const auth = await apiOwner();
  if (!auth.user) return auth.response;

  const { id } = await params;
  const assetId = Number(id);
  if (!Number.isInteger(assetId)) {
    return Response.json({ error: "Invalid image." }, { status: 400 });
  }

  const removed = await deleteMedia(assetId).catch(() => false);
  if (!removed) {
    return Response.json({ error: "Image not found." }, { status: 404 });
  }

  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: "media.delete",
    targetType: "media_asset",
    targetId: assetId,
  });

  return Response.json({ ok: true });
}
