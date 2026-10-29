import { requireStaff } from "@/lib/auth";
import { pendingDepositRows, pendingWithdrawalRows } from "@/lib/admin";
import AdminRequestsTable from "@/components/AdminRequestsTable";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminRequestsPage() {
  const me = await requireStaff();
  const [deposits, withdrawals] = await Promise.all([
    pendingDepositRows(),
    pendingWithdrawalRows(),
  ]);
  const canApprove = me.role === "admin" || me.role === "owner";

  const depositRows = deposits.map((d) => ({
    id: d.id,
    userId: d.userId,
    userEmail: d.userEmail,
    userName: d.userName,
    amountUsd: d.amountUsd,
    status: d.status,
    createdAt: new Date(d.createdAt).toISOString(),
    reference: d.reference,
    destination: null,
  }));

  const withdrawalRows = withdrawals.map((w) => ({
    id: w.id,
    userId: w.userId,
    userEmail: w.userEmail,
    userName: w.userName,
    amountUsd: w.amountUsd,
    status: w.status,
    createdAt: new Date(w.createdAt).toISOString(),
    reference: null,
    destination: w.destination,
  }));

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Approvals"
        description="Deposit and withdrawal requests waiting on an operator."
      />

      {!canApprove && (
        <Notice tone="info">
          Your role can review the queue but not approve it. Ask an admin to sign
          off.
        </Notice>
      )}

      <div className="grid gap-6 xl:grid-cols-2">
        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Deposits</h2>
            <span className="rounded-full bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold text-[#c7cedb]">
              {deposits.length} pending
            </span>
          </div>
          <AdminRequestsTable
            kind="deposit"
            canApprove={canApprove}
            rows={depositRows}
          />
        </div>

        <div className="rounded-2xl border border-line bg-card">
          <div className="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Withdrawals</h2>
            <span className="rounded-full bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold text-[#c7cedb]">
              {withdrawals.length} pending
            </span>
          </div>
          <AdminRequestsTable
            kind="withdrawal"
            canApprove={canApprove}
            rows={withdrawalRows}
          />
        </div>
      </div>

      <Notice tone="warn" title="Approval posts to the ledger">
        Approving a deposit debits custody cash and credits the
        user&apos;s wallet liability. Approving a withdrawal releases the reserved
        funds out of custody. Both write an audit entry with your identity.
      </Notice>
    </div>
  );
}
