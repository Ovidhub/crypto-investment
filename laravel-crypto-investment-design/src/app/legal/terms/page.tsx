import LegalLayout from "@/components/LegalLayout";

export const dynamic = "force-dynamic";

export default function TermsPage() {
  return (
    <LegalLayout title="Terms of use" updated="28 October 2026">
      <section>
        <h2 className="font-display text-lg font-bold text-white">1. What this service is</h2>
        <p className="mt-2">
          Enethi is a crypto investment platform. It lets you fund a wallet, buy and
          hold crypto assets, subscribe to managed plans and take positions on the
          trade desk. By opening an account you accept these terms.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">2. Eligibility and your profile</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>You must be at least 18 years old and legally able to enter this agreement.</li>
          <li>
            You must complete the onboarding profile before using the dashboard, and
            keep the details accurate so statements, verification and payouts reach
            the right person.
          </li>
          <li>One account per person. Accounts are not transferable.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">3. Acceptable use</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>Do not attempt to break authentication, rate limits or the ledger invariants.</li>
          <li>Do not use another person&apos;s identity or documents without their consent.</li>
          <li>Do not use the platform for fraud, money laundering or any unlawful purpose.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">4. Accounts and balances</h2>
        <p className="mt-2">
          Deposits and withdrawals are reviewed before they post. Every balance is
          backed by an entry in the platform ledger. The operator may suspend or close
          an account that breaches these terms, and may correct entries made in error.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">5. No warranty</h2>
        <p className="mt-2">
          The service is provided &quot;as is&quot;, without warranties of any kind.
          Investment carries risk, and the operator is not liable for losses caused by
          market movement or by your own trading decisions. Liability is limited to
          the maximum permitted by applicable law.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">6. Changes</h2>
        <p className="mt-2">
          These terms may change as the service evolves. The date at the top of this
          page reflects the latest revision, and material changes are announced in
          your account notifications.
        </p>
      </section>
    </LegalLayout>
  );
}
