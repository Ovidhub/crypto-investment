import { getCurrentUser } from "@/lib/auth";
import { getTransactions } from "@/lib/portfolio";
import { formatUsd } from "@/lib/format";

export const dynamic = "force-dynamic";

const TYPE_STYLE: Record<string, string> = {
  buy: "bg-accent/15 text-accent",
  deposit: "bg-accent/15 text-accent",
  sell: "bg-[#f6465d]/15 text-[#f6465d]",
  withdraw: "bg-[#f6465d]/15 text-[#f6465d]",
};

export default async function TransactionsPage() {
  const me = (await getCurrentUser())!;
  const txns = await getTransactions(me.id, 100);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">
          Transactions
        </h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          A complete history of your account activity.
        </p>
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {txns.length === 0 ? (
          <p className="p-10 text-center text-sm text-[#9aa3b2]">
            No transactions yet.
          </p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[640px] text-left text-sm">
              <thead>
                <tr className="border-b border-line text-xs uppercase tracking-wider text-[#7c8698]">
                  <th className="px-6 py-4 font-medium">Type</th>
                  <th className="px-6 py-4 font-medium">Asset</th>
                  <th className="px-6 py-4 text-right font-medium">Amount</th>
                  <th className="px-6 py-4 text-right font-medium">Price</th>
                  <th className="px-6 py-4 text-right font-medium">Value</th>
                  <th className="px-6 py-4 text-right font-medium">Date</th>
                </tr>
              </thead>
              <tbody>
                {txns.map((t) => (
                  <tr
                    key={t.id}
                    className="border-b border-line/60 last:border-0"
                  >
                    <td className="px-6 py-4">
                      <span
                        className={`rounded-full px-2.5 py-1 text-xs font-semibold capitalize ${TYPE_STYLE[t.type] ?? "bg-white/10 text-white"}`}
                      >
                        {t.type}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-[#c7cedb]">
                      {t.symbol ?? "USD"}
                    </td>
                    <td className="px-6 py-4 text-right tabular-nums text-[#c7cedb]">
                      {t.amount ? Number(t.amount).toFixed(6) : "—"}
                    </td>
                    <td className="px-6 py-4 text-right tabular-nums text-[#c7cedb]">
                      {t.price ? formatUsd(Number(t.price)) : "—"}
                    </td>
                    <td className="px-6 py-4 text-right font-semibold tabular-nums text-white">
                      {formatUsd(Number(t.usdValue))}
                    </td>
                    <td className="px-6 py-4 text-right text-[#7c8698]">
                      {new Date(t.createdAt).toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                      })}
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
