import { inArray } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { requireOwner } from "@/lib/auth";
import TeamManager from "@/components/TeamManager";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminTeamPage() {
  const me = await requireOwner();

  const rows = await db
    .select({
      id: users.id,
      name: users.name,
      email: users.email,
      role: users.role,
      status: users.status,
    })
    .from(users)
    .where(inArray(users.role, ["support", "admin", "owner"]))
    .orderBy(users.id);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Team & roles"
        description="Who can sign in to the admin console, and what each of them may do."
      />
      <Notice tone="warn" title="Roles">
        <ul className="mt-1 list-disc space-y-1 pl-4">
          <li><strong>Support</strong> — sees queues and customers, cannot approve money movement.</li>
          <li><strong>Admin</strong> — approvals, adjustments, plans, users, ledger.</li>
          <li><strong>Super admin</strong> — everything, including this page, website content and images.</li>
        </ul>
      </Notice>
      <TeamManager
        currentId={me.id}
        rows={rows.map((r) => ({
          id: r.id,
          name: r.name,
          email: r.email,
          role: r.role,
          status: r.status,
        }))}
      />
    </div>
  );
}
