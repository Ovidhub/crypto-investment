import { requireStaff } from "@/lib/auth";
import { trialBalance, recentJournals } from "@/lib/ledger";
import { formatUsd } from "@/lib/format";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminBooksPage() {
  await requireStaff();
  const [books, journals] = await Promise.all([trialBalance(), recentJournals(25)]);

  const grouped = books.accounts.reduce<Record<string, typeof books.accounts>>(
    (acc, account) => {
      acc[account.kind] = acc[account.kind] ?? [];
      acc[account.kind].push(account);
      return acc;
    },
    {},
  );

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Ledger"
        description="Trial balance and the most recent journals. Every money movement in the app lands here first."
      />

      <div className="grid gap-4 sm:grid-cols-3">
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Total debits</p>
          <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
            {formatUsd(Number(books.debits))}
          </p>
        </div>
        <div className="rounded-2xl border border-line bg-card p-5">
          <p className="text-xs text-[#9aa3b2]">Total credits</p>
          <p className="mt-2 font-display text-2xl font-bold tabular-nums text-white">
            {formatUsd(Number(books.credits))}
          </p>
        </div>
        <div
          className={`rounded-2xl border p-5 ${
            books.balanced
              ? "border-accent/30 bg-accent/5"
              : "border-[#f6465d]/30 bg-[#f6465d]/5"
          }`}
        >
          <p className="text-xs text-[#9aa3b2]">Difference</p>
          <p
            className={`mt-2 font-display text-2xl font-bold tabular-nums ${
              books.balanced ? "text-accent" : "text-[#f6465d]"
            }`}
          >
            {formatUsd(Number(books.difference))}
          </p>
          <p className="text-[11px] text-[#7c8698]">
            {books.balanced ? "Books are balanced" : "Investigate immediately"}
          </p>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
        <div className="rounded-2xl border border-line bg-card">
          <div className="border-b border-line px-6 py-4">
            <h2 className="font-display font-bold">Trial balance</h2>
            <p className="text-xs text-[#7c8698]">Balances shown in each account&apos;s natural direction.</p>
          </div>
          <div className="divide-y divide-line/60">
            {Object.entries(grouped).map(([kind, accounts]) => (
              <div key={kind} className="px-6 py-4">
                <p className="text-[11px] font-bold uppercase tracking-wider text-[#5d6675]">
                  {kind}
                </p>
                <div className="mt-2 space-y-1.5">
                  {accounts.map((a) => (
                    <div key={a.code} className="flex items-center justify-between gap-3 text-sm">
                      <div className="min-w-0">
                        <p className="truncate text-[#c7cedb]">{a.name}</p>
                        <p className="truncate font-mono text-[10px] text-[#5d6675]">{a.code}</p>
                      </div>
                      <span className="shrink-0 tabular-nums text-white">
                        {formatUsd(a.balanceNumber)}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="space-y-4">
          <div className="rounded-2xl border border-line bg-card">
            <div className="border-b border-line px-6 py-4">
              <h2 className="font-display font-bold">Recent journals</h2>
            </div>
            <div className="divide-y divide-line/60">
              {journals.map((j) => (
                <div key={j.id} className="px-6 py-3.5">
                  <div className="flex items-start justify-between gap-3">
                    <p className="text-sm text-[#c7cedb]">{j.memo}</p>
                    <span className="shrink-0 font-mono text-[10px] text-[#5d6675]">
                      #{j.id}
                    </span>
                  </div>
                  <p className="text-[11px] text-[#7c8698]">
                    {j.kind} · {new Date(j.createdAt).toLocaleString("en-US")}
                  </p>
                </div>
              ))}
            </div>
          </div>

          <Notice tone="info" title="Controls">
            Journals reject themselves if debits and credits differ, if a leg is
            zero, or if an entry has fewer than two legs. The check runs before
            any write.
          </Notice>
        </div>
      </div>
    </div>
  );
}
