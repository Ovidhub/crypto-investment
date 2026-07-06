import { getCurrentUser } from "@/lib/auth";
import { getRecentUsers } from "@/lib/admin";
import AdminUsersTable from "@/components/AdminUsersTable";

export const dynamic = "force-dynamic";

export default async function AdminUsersPage() {
  const me = (await getCurrentUser())!;
  const rows = await getRecentUsers(200);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">Users</h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          Manage accounts, roles and access.
        </p>
      </div>
      <AdminUsersTable
        rows={rows.map((r) => ({ ...r, createdAt: r.createdAt.toISOString() }))}
        currentId={me.id}
      />
    </div>
  );
}
