import { desc, eq } from "drizzle-orm";
import { db } from "@/db";
import { mediaAssets } from "@/db/schema";

/**
 * Uploaded images.
 *
 * Vercel functions have a read-only filesystem, so uploads live in Postgres
 * as a base64 payload and are streamed back from /api/media/[id]. Small,
 * cacheable and it survives redeploys.
 */

export const MAX_UPLOAD_BYTES = 2 * 1024 * 1024; // 2 MB

export const ALLOWED_MIME = [
  "image/png",
  "image/jpeg",
  "image/webp",
  "image/gif",
  "image/avif",
] as const;

export type MediaItem = {
  id: number;
  filename: string;
  mime: string;
  sizeBytes: number;
  url: string;
  createdAt: string;
};

export type CreateMediaResult =
  | { ok: true; item: MediaItem }
  | { ok: false; error: string };

function toItem(row: {
  id: number;
  filename: string;
  mime: string;
  sizeBytes: number;
  createdAt: Date | string;
}): MediaItem {
  return {
    id: row.id,
    filename: row.filename,
    mime: row.mime,
    sizeBytes: row.sizeBytes,
    url: `/api/media/${row.id}`,
    createdAt: new Date(row.createdAt).toISOString(),
  };
}

export async function listMedia(limit = 60): Promise<MediaItem[]> {
  try {
    const rows = await db
      .select({
        id: mediaAssets.id,
        filename: mediaAssets.filename,
        mime: mediaAssets.mime,
        sizeBytes: mediaAssets.sizeBytes,
        createdAt: mediaAssets.createdAt,
      })
      .from(mediaAssets)
      .orderBy(desc(mediaAssets.id))
      .limit(limit);
    return rows.map(toItem);
  } catch {
    return [];
  }
}

export async function getMediaBinary(
  id: number,
): Promise<{ mime: string; filename: string; buffer: Buffer } | null> {
  try {
    const [row] = await db
      .select({
        mime: mediaAssets.mime,
        filename: mediaAssets.filename,
        data: mediaAssets.data,
      })
      .from(mediaAssets)
      .where(eq(mediaAssets.id, id))
      .limit(1);
    if (!row) return null;
    return {
      mime: row.mime,
      filename: row.filename,
      buffer: Buffer.from(row.data, "base64"),
    };
  } catch {
    return null;
  }
}

export async function createMedia(
  input: { filename: string; mime: string; bytes: Buffer },
  actorId: number | null,
): Promise<CreateMediaResult> {
  if (!ALLOWED_MIME.includes(input.mime as (typeof ALLOWED_MIME)[number])) {
    return {
      ok: false,
      error: "Upload a PNG, JPEG, WEBP, GIF or AVIF image.",
    };
  }
  if (input.bytes.byteLength === 0) {
    return { ok: false, error: "The file is empty." };
  }
  if (input.bytes.byteLength > MAX_UPLOAD_BYTES) {
    return { ok: false, error: "Images must be 2 MB or smaller." };
  }

  const [created] = await db
    .insert(mediaAssets)
    .values({
      filename: input.filename.trim().slice(0, 160) || "image",
      mime: input.mime,
      sizeBytes: input.bytes.byteLength,
      data: input.bytes.toString("base64"),
      uploadedBy: actorId,
    })
    .returning({
      id: mediaAssets.id,
      filename: mediaAssets.filename,
      mime: mediaAssets.mime,
      sizeBytes: mediaAssets.sizeBytes,
      createdAt: mediaAssets.createdAt,
    });

  return { ok: true, item: toItem(created) };
}

export async function deleteMedia(id: number): Promise<boolean> {
  const deleted = await db
    .delete(mediaAssets)
    .where(eq(mediaAssets.id, id))
    .returning({ id: mediaAssets.id });
  return deleted.length > 0;
}
