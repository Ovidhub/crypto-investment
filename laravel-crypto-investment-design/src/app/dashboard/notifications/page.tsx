import { requireUser } from "@/lib/auth";
import { listNotifications } from "@/lib/notifications";
import NotificationCenter from "@/components/NotificationCenter";
import SectionHeader from "@/components/SectionHeader";

export const dynamic = "force-dynamic";

export default async function NotificationsPage() {
  const me = await requireUser();
  const items = await listNotifications(me.id);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Notifications"
        description="Accruals, approvals and verification decisions land here."
      />
      <NotificationCenter
        items={items.map((n) => ({
          id: n.id,
          title: n.title,
          body: n.body,
          readAt: n.readAt ? new Date(n.readAt).toISOString() : null,
          createdAt: new Date(n.createdAt).toISOString(),
        }))}
      />
    </div>
  );
}
