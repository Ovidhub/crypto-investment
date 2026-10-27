import { requireUser } from "@/lib/auth";
import DashboardShell from "@/components/DashboardShell";
import { unreadCount } from "@/lib/notifications";

export const dynamic = "force-dynamic";

export default async function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const me = await requireUser();
  const unread = await unreadCount(me.id);

  return (
    <DashboardShell
      user={{ name: me.name, email: me.email, role: me.role }}
      variant="user"
      unread={unread}
    >
      {children}
    </DashboardShell>
  );
}
