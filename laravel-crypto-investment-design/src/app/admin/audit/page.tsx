import { requireStaff } from "@/lib/auth";
import { getAuditTrail } from "@/lib/admin";
import SectionHeader from "@/components/SectionHeader";
import EmptyState from "@/components/EmptyState";

export const dynamic = "force-dynamic";

export default async function AdminAuditPage() {
  await requireStaff();
  const rows = await getAuditTrail(150);

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Audit trail"
        description="Append-only record of privileged actions, sign-ins and ledger runs."
      />

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {rows.length === 0 ? (
          <EmptyState title="No entries yet" body="Actions will appear here as they happen." />
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[780px] text-sm">
              <thead className="border-b border-line text-left text-[11px] uppercase tracking-wider text-[#7c8698]">
                <tr>
                  <th className="px-6 py-3 font-semibold">When</th>
                  <th className="px-6 py-3 font-semibold">Actor</th>
                  <th className="px-6 py-3 font-semibold">Action</th>
                  <th className="px-6 py-3 font-semibold">Target</th>
                  <th className="px-6 py-3 font-semibold">Meta</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-line/60">
                {rows.map((r) => (
                  <tr key={r.id}>
                    <td className="whitespace-nowrap px-6 py-3 text-xs text-[#7c8698]">
                      {new Date(r.createdAt).toLocaleString("en-US")}
                    </td>
                    <td className="px-6 py-3 text-xs text-[#c7cedb]">
                      {r.actorEmail ?? (r.actorId ? `user #${r.actorId}` : "system")}
                    </td>
                    <td className="px-6 py-3">
                      <span className="rounded-md bg-white/5 px-2 py-0.5 font-mono text-[11px] text-brand">
                        {r.action}
                      </span>
                    </td>
                    <td className="px-6 py-3 text-xs text-[#9aa3b2]">
                      {r.targetType ? `${r.targetType}${r.targetId ? ` #${r.targetId}` : ""}` : "—"}
                    </td>
                    <td className="max-w-[280px] truncate px-6 py-3 font-mono text-[10px] text-[#5d6675]">
                      {r.meta ?? "—"}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
