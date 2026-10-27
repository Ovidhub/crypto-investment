import LegalLayout from "@/components/LegalLayout";

export const dynamic = "force-dynamic";

export default function TermsPage() {
  return (
    <LegalLayout title="Terms of use" updated="28 October 2026">
      <section>
        <h2 className="font-display text-lg font-bold text-white">1. What this service is</h2>
        <p className="mt-2">
          Enethi is a non-commercial demonstration of crypto investment software.
          By using it you accept that it provides no financial service and creates
          no financial obligation between you and the operator.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">2. Acceptable use</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>Do not attempt to break authentication, rate limits or the ledger invariants.</li>
          <li>Do not enter another person&apos;s personal data or real identity documents.</li>
          <li>Do not use the demo to mislead anyone into believing it is a live platform.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">3. Accounts</h2>
        <p className="mt-2">
          Accounts are for demonstration. The operator may suspend, reset or delete
          any account and its data at any time, including data created during
          testing.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">4. No warranty</h2>
        <p className="mt-2">
          The software is provided &quot;as is&quot;, without warranties of any
          kind. The operator is not liable for any loss arising from use of the
          demo, including loss of demo data.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">5. Changes</h2>
        <p className="mt-2">
          These terms may change as the demo evolves. The date at the top of this
          page reflects the latest revision.
        </p>
      </section>
    </LegalLayout>
  );
}
