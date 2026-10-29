import { getMediaBinary } from "@/lib/media";

export const dynamic = "force-dynamic";

/**
 * Serve an uploaded image. Public: the marketing pages embed these URLs, and
 * they are only images the super admin chose to publish.
 */
export async function GET(
  _req: Request,
  { params }: { params: Promise<{ id: string }> },
) {
  const { id } = await params;
  const assetId = Number(id);
  if (!Number.isInteger(assetId) || assetId <= 0) {
    return new Response("Not found", { status: 404 });
  }

  const asset = await getMediaBinary(assetId);
  if (!asset) return new Response("Not found", { status: 404 });

  return new Response(new Uint8Array(asset.buffer), {
    headers: {
      "content-type": asset.mime,
      "content-length": String(asset.buffer.byteLength),
      "cache-control": "public, max-age=31536000, immutable",
      "content-disposition": `inline; filename="${asset.filename.replace(/"/g, "")}"`,
      "x-content-type-options": "nosniff",
    },
  });
}
