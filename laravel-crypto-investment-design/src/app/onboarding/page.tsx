import Link from "next/link";
import { redirect } from "next/navigation";
import { requireUser } from "@/lib/auth";
import { getProfile } from "@/lib/profile";
import ProfileForm from "@/components/ProfileForm";
import BrandMark from "@/components/BrandMark";

export const dynamic = "force-dynamic";

export default async function OnboardingPage({
  searchParams,
}: {
  searchParams: Promise<{ edit?: string }>;
}) {
  const me = await requireUser();
  const { edit } = await searchParams;
  const profile = await getProfile(me.id);
  const editing = edit === "1" && profile.complete;

  // Already done and not editing: nothing to onboard.
  if (profile.complete && !editing) redirect("/dashboard");

  return (
    <div className="relative min-h-screen px-5 py-12">
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute inset-0 grid-bg" />
        <div className="absolute left-1/2 top-0 h-[380px] w-[680px] -translate-x-1/2 rounded-full bg-brand-2/20 blur-[130px]" />
      </div>

      <div className="mx-auto w-full max-w-2xl">
        <div className="mb-8 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2.5">
            <BrandMark size={32} />
            <span className="font-display text-xl font-bold">Actuatrades</span>
          </Link>
          <span className="text-xs text-[#7c8698]">
            {editing ? "Profile settings" : "Step 1 of 1 · takes about a minute"}
          </span>
        </div>

        <ProfileForm
          initial={{
            phone: profile.phone,
            country: profile.country,
            dateOfBirth: profile.dateOfBirth,
            addressLine: profile.addressLine,
            city: profile.city,
            postalCode: profile.postalCode,
            occupation: profile.occupation,
            tradingExperience: profile.tradingExperience,
            investmentGoal: profile.investmentGoal,
          }}
          firstName={me.name.split(" ")[0]}
          editing={editing}
        />
      </div>
    </div>
  );
}
