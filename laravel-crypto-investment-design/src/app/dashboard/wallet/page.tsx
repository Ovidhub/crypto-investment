import { getCurrentUser } from "@/lib/auth";
import WalletPanel from "@/components/WalletPanel";

export const dynamic = "force-dynamic";

export default async function WalletPage() {
  const me = (await getCurrentUser())!;
  return (
    <div className="space-y-6">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">Wallet</h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          Manage your cash balance — deposit or withdraw funds.
        </p>
      </div>
      <WalletPanel balance={Number(me.balanceUsd)} />
    </div>
  );
}
