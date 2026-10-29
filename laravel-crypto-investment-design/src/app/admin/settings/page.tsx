import { requireAdmin } from "@/lib/auth";
import { allFlags } from "@/lib/wallet";
import FlagToggles from "@/components/FlagToggles";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

export default async function AdminSettingsPage() {
  await requireAdmin();
  const flags = await allFlags();

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Settings"
        description="Kill switches for each money-moving surface."
      />
      <div className="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
        <FlagToggles
          flags={flags.map((f) => ({
            key: f.key,
            enabled: f.enabled,
            updatedAt: new Date(f.updatedAt).toISOString(),
          }))}
        />
        <div className="space-y-4">
          <Notice tone="info" title="How flags behave">
            A disabled flag rejects new requests with a 503 and a clear message in
            the UI. Existing balances, positions and accruals are unaffected.
          </Notice>
          <div className="rounded-2xl border border-line bg-panel-2 p-5 text-xs text-[#7c8698]">
            <p className="font-semibold text-[#c7cedb]">Not exposed on purpose</p>
            <ul className="mt-2 list-disc space-y-1 pl-4">
              <li>No manual balance edit — credit only flows through the ledger.</li>
              <li>No impersonation — sessions cannot be minted for other users.</li>
              <li>No scheduled-job toggle — accrual stays behind CRON_SECRET.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
