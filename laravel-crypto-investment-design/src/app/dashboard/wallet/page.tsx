import { requireUser } from "@/lib/auth";
import { userBalances } from "@/lib/ledger";
import { myRequests } from "@/lib/wallet";
import { centsToUsd } from "@/lib/money";
import WalletPanel from "@/components/WalletPanel";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function WalletPage() {
  const me = await requireUser();
  const [balances, requests] = await Promise.all([
    userBalances(me.id),
    myRequests(me.id),
  ]);

  const rows = [
    ...requests.deposits.map((d) => ({
      id: d.id,
      type: "deposit" as const,
      amountUsd: d.amountUsd,
      status: d.status,
      reference: d.reference,
      destination: null,
      createdAt: new Date(d.createdAt).toISOString(),
      adminNote: d.adminNote,
    })),
    ...requests.withdrawals.map((w) => ({
      id: w.id,
      type: "withdrawal" as const,
      amountUsd: w.amountUsd,
      status: w.status,
      reference: null,
      destination: w.destination,
      createdAt: new Date(w.createdAt).toISOString(),
      adminNote: w.adminNote,
    })),
  ].sort((a, b) => b.createdAt.localeCompare(a.createdAt));

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Wallet"
        description="Deposits and withdrawals, reviewed by an operator before they post."
      />
      <Notice tone="info">
        No payment processor is connected. Deposit requests only create a review
        item; approving one credits virtual funds through the ledger.
      </Notice>
      <WalletPanel
        cash={centsToUsd(balances.cash)}
        locked={centsToUsd(balances.locked)}
        requests={rows}
      />
    </div>
  );
}
