import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import DashboardShell from "@/components/DashboardShell";
import type { ReactNode } from "react";

export const dynamic = "force-dynamic";

export default async function AdminLayout({
  children,
}: {
  children: ReactNode;
}) {
  const me = await getCurrentUser();
  if (!me) redirect("/login");
  if (me.role !== "admin") redirect("/dashboard");

  return (
    <DashboardShell
      user={{ name: me.name, email: me.email, role: me.role }}
      variant="admin"
    >
      {children}
    </DashboardShell>
  );
}
