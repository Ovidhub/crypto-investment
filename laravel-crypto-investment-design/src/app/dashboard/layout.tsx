import { redirect } from "next/navigation";
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

  // A customer must finish onboarding before the product opens up.
  if (!me.profileCompletedAt) redirect("/onboarding");

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
