import Link from "next/link";
import { requireUser } from "@/lib/auth";
import SettingsPanels from "@/components/SettingsPanels";
import SectionHeader from "@/components/SectionHeader";
import StatusPill from "@/components/StatusPill";

export const dynamic = "force-dynamic";

export default async function SettingsPage() {
  const me = await requireUser();

  return (
    <div className="space-y-8">
      <SectionHeader title="Settings" description="Account security and data controls." />

      <div className="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <SettingsPanels email={me.email} />

        <div className="space-y-4">
          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="font-display font-bold">Profile</h2>
            <dl className="mt-4 space-y-2 text-sm">
              <div className="flex justify-between gap-4">
                <dt className="text-[#7c8698]">Name</dt>
                <dd className="text-right text-[#c7cedb]">{me.name}</dd>
              </div>
              <div className="flex justify-between gap-4">
                <dt className="text-[#7c8698]">Email</dt>
                <dd className="truncate text-right text-[#c7cedb]">{me.email}</dd>
              </div>
              <div className="flex justify-between gap-4">
                <dt className="text-[#7c8698]">Role</dt>
                <dd className="text-right capitalize text-[#c7cedb]">{me.role}</dd>
              </div>
              <div className="flex justify-between gap-4">
                <dt className="text-[#7c8698]">Verification</dt>
                <dd className="text-right">
                  <StatusPill status={me.kycStatus} />
                </dd>
              </div>
              <div className="flex justify-between gap-4">
                <dt className="text-[#7c8698]">Referral code</dt>
                <dd className="text-right font-mono text-[#c7cedb]">{me.referralCode}</dd>
              </div>
            </dl>
          </div>

          <div className="rounded-2xl border border-line bg-panel-2 p-5 text-xs text-[#7c8698]">
            <p className="font-semibold text-[#c7cedb]">Security notes</p>
            <ul className="mt-2 list-disc space-y-1 pl-4">
              <li>Sessions are signed, HTTP-only cookies valid for 7 days.</li>
              <li>Passwords are stored with scrypt and a per-user salt.</li>
              <li>Login attempts are rate limited per IP.</li>
              <li>
                Two-factor authentication and device history are on the roadmap,
                not yet implemented.
              </li>
            </ul>
          </div>

          <Link
            href="/dashboard/support"
            className="block rounded-xl border border-line px-5 py-3 text-center text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
          >
            Need help? Contact support
          </Link>
        </div>
      </div>
    </div>
  );
}
