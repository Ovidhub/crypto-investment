import { requireAdmin } from "@/lib/auth";
import { usersWithMeta } from "@/lib/admin";
import AdminUsersTable from "@/components/AdminUsersTable";
import SectionHeader from "@/components/SectionHeader";

export const dynamic = "force-dynamic";

export default async function AdminUsersPage() {
  const me = await requireAdmin();
  const rows = await usersWithMeta(300);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Users"
        description="Roles, access and balances. Every change is written to the audit trail."
      />
      <AdminUsersTable
        rows={rows.map((r) => ({ ...r, createdAt: r.createdAt.toISOString() }))}
        currentId={me.id}
      />
    </div>
  );
}
