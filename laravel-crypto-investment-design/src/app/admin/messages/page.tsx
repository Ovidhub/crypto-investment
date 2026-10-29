import { requireStaff } from "@/lib/auth";
import { getMessages } from "@/lib/admin";
import SectionHeader from "@/components/SectionHeader";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

export default async function AdminMessagesPage() {
  await requireStaff();
  const rows = await getMessages();

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Messages"
        description="Inbound contact-form submissions."
      />
      <div className="rounded-2xl border border-line bg-card">
        {rows.length === 0 ? (
          <EmptyState title="Inbox empty" body="Contact-form messages will show up here." />
        ) : (
          <div className="divide-y divide-line/60">
            {rows.map((m) => (
              <div key={m.id} className="px-6 py-4">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <p className="text-sm font-semibold text-white">
                    {m.name} <span className="font-normal text-[#7c8698]">· {m.email}</span>
                  </p>
                  <span className="text-[11px] text-[#7c8698]">
                    {new Date(m.createdAt).toLocaleString("en-US")}
                  </span>
                </div>
                <p className="mt-2 whitespace-pre-wrap text-sm text-[#9aa3b2]">{m.message}</p>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
