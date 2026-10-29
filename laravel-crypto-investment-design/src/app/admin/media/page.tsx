import { requireOwner } from "@/lib/auth";
import { listMedia } from "@/lib/media";
import MediaManager from "@/components/MediaManager";
import SectionHeader from "@/components/SectionHeader";

export const dynamic = "force-dynamic";

export default async function AdminMediaPage() {
  await requireOwner();
  const items = await listMedia(120);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Media library"
        description="Every image you have uploaded. Copy a URL to reuse it in any content field."
      />
      <MediaManager
        initial={items.map((item) => ({
          id: item.id,
          filename: item.filename,
          mime: item.mime,
          sizeBytes: item.sizeBytes,
          url: item.url,
          createdAt: item.createdAt,
        }))}
      />
    </div>
  );
}
