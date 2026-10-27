import Link from "next/link";
import { requireUser } from "@/lib/auth";
import SectionHeader from "@/components/SectionHeader";
import Notice from "@/components/Notice";

export const dynamic = "force-dynamic";

const FAQ = [
  {
    q: "Is any of this real money?",
    a: "No. Every balance is simulated. There is no payment processor, no custody and no blockchain transaction anywhere in the product.",
  },
  {
    q: "Why did my deposit stay pending?",
    a: "Deposits and withdrawals are deliberately manual. An admin reviews each request in the approvals queue before virtual funds move.",
  },
  {
    q: "How is the plan accrual calculated?",
    a: "principal × annual rate ÷ 365, credited once a day while the plan is active. It is a model output, not a market return.",
  },
  {
    q: "Can I withdraw?",
    a: "You can request a simulated withdrawal. It reserves your balance, then an admin approves or rejects it. Nothing is sent to a real address.",
  },
  {
    q: "What happens to my data in demo mode?",
    a: "The demo database lives in server memory and resets on a cold start. Configure DATABASE_URL for persistence.",
  },
];

export default async function SupportPage() {
  const me = await requireUser();

  return (
    <div className="space-y-8">
      <SectionHeader
        title="Support"
        description="Answers to the questions this demo raises most often."
      />

      <div className="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <div className="space-y-3">
          {FAQ.map((item) => (
            <details
              key={item.q}
              className="group rounded-2xl border border-line bg-card px-5 py-4"
            >
              <summary className="cursor-pointer list-none font-semibold text-white">
                {item.q}
              </summary>
              <p className="mt-2 text-sm text-[#9aa3b2]">{item.a}</p>
            </details>
          ))}
        </div>

        <div className="space-y-4">
          <div className="rounded-2xl border border-line bg-card p-6">
            <h2 className="font-display font-bold">Contact a human</h2>
            <p className="mt-2 text-sm text-[#9aa3b2]">
              Send a message through the contact form and it lands in the admin
              inbox.
            </p>
            <Link
              href="/contact"
              className="mt-4 inline-block rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
            >
              Open contact form
            </Link>
          </div>
          <Notice tone="info">
            Signed in as {me.email}. Include your account email so support can
            find the right ledger entries.
          </Notice>
        </div>
      </div>
    </div>
  );
}
