import { requireOwner } from "@/lib/auth";
import { CONTENT_GROUPS, getSiteContent } from "@/lib/content";
import { listMedia } from "@/lib/media";
import ContentEditor from "@/components/ContentEditor";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminContentPage() {
  await requireOwner();
  const [content, media] = await Promise.all([getSiteContent(), listMedia(60)]);

  const groups = CONTENT_GROUPS.map((group) => ({
    id: group.id,
    title: group.title,
    description: group.description,
    fields: group.fields.map((field) => ({
      key: field.key,
      label: field.label,
      type: field.type,
      value: content[field.key] ?? field.value,
      help: field.help,
      maxLength: field.maxLength,
    })),
  }));

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Website content"
        description="Edit the words and pictures on the public site. Changes appear immediately after saving."
      />
      <Notice tone="info" title="How this works">
        Every field here is live copy. Leave a field blank to fall back to the
        built-in default, and wrap a word in *asterisks* to give it the brand
        gradient in headlines.
      </Notice>
      <ContentEditor
        groups={groups}
        media={media.map((item) => ({
          id: item.id,
          url: item.url,
          filename: item.filename,
        }))}
      />
    </div>
  );
}
