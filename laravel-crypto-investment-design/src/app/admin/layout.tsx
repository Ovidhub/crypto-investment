import { requireStaff } from "@/lib/auth";
import DashboardShell from "@/components/DashboardShell";

export const dynamic = "force-dynamic";

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const me = await requireStaff();

  return (
    <DashboardShell
      user={{ name: me.name, email: me.email, role: me.role }}
      variant="admin"
    >
      {children}
    </DashboardShell>
  );
}
