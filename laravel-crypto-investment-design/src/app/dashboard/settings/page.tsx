import Link from "next/link";
import { requireUser } from "@/lib/auth";
import {
  INVESTMENT_GOALS,
  TRADING_EXPERIENCE,
  getProfile,
} from "@/lib/profile";
import SettingsPanels from "@/components/SettingsPanels";
import SectionHeader from "@/components/SectionHeader";
import StatusPill from "@/components/StatusPill";

export const dynamic = "force-dynamic";

export default async function SettingsPage() {
  const me = await requireUser();
  const profile = await getProfile(me.id);
  const experience =
    TRADING_EXPERIENCE.find((o) => o.key === profile.tradingExperience)?.label ?? "—";
  const goal = INVESTMENT_GOALS.find((o) => o.key === profile.investmentGoal)?.label ?? "—";

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

            <div className="mt-5 border-t border-line pt-4">
              <div className="flex items-center justify-between gap-3">
                <h3 className="text-xs font-semibold uppercase tracking-wider text-[#7c8698]">
                  Contact &amp; profile
                </h3>
                <Link href="/onboarding?edit=1" className="text-xs font-semibold text-brand hover:underline">
                  Edit
                </Link>
              </div>
              <dl className="mt-3 space-y-2 text-sm">
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Phone</dt>
                  <dd className="text-right text-[#c7cedb]">{profile.phone || "—"}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Country</dt>
                  <dd className="text-right text-[#c7cedb]">{profile.country || "—"}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Date of birth</dt>
                  <dd className="text-right text-[#c7cedb]">{profile.dateOfBirth || "—"}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Address</dt>
                  <dd className="max-w-[60%] text-right text-[#c7cedb]">
                    {profile.addressLine
                      ? [profile.addressLine, profile.city, profile.postalCode]
                          .filter(Boolean)
                          .join(", ")
                      : "—"}
                  </dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Occupation</dt>
                  <dd className="text-right text-[#c7cedb]">{profile.occupation || "—"}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Experience</dt>
                  <dd className="text-right text-[#c7cedb]">{experience}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Goal</dt>
                  <dd className="text-right text-[#c7cedb]">{goal}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-[#7c8698]">Profile</dt>
                  <dd className="text-right">
                    <StatusPill status={profile.complete ? "complete" : "pending"} />
                  </dd>
                </div>
              </dl>
            </div>
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
