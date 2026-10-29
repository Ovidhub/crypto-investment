import LegalLayout from "@/components/LegalLayout";

export const dynamic = "force-dynamic";

export default function PrivacyPage() {
  return (
    <LegalLayout title="Privacy notice" updated="28 October 2026">
      <section>
        <h2 className="font-display text-lg font-bold text-white">What we store</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>Your name, email address and a scrypt hash of your password.</li>
          <li>
            The profile you complete at onboarding: phone number, country, date of
            birth, address, city, postal code and occupation, plus your stated
            trading experience and investment goal.
          </li>
          <li>Ledger entries, positions and trades created by your activity.</li>
          <li>A signed session cookie so you stay signed in. It contains no password.</li>
          <li>
            For identity verification: your name, country, document type and the
            last four digits you type. No images and no full document numbers.
          </li>
          <li>Audit records for privileged actions, including the actor&apos;s email.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">How we use it</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>To operate your account, settle transactions and issue statements.</li>
          <li>
            To meet verification and record-keeping obligations, and to review
            deposit and withdrawal requests.
          </li>
          <li>To contact you about your account, security events and service changes.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">What we do not do</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>We never store full card numbers or wallet private keys.</li>
          <li>No analytics or advertising trackers are loaded by this app.</li>
          <li>No data is sold or shared with third parties for marketing.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">Third parties</h2>
        <p className="mt-2">
          CoinGecko&apos;s public API is queried from the server for market prices.
          Your account details are not sent to it. Hosting is provided by Vercel,
          which processes standard request logs.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">Your rights</h2>
        <p className="mt-2">
          You can review and correct your profile at any time in Settings, and
          download a JSON export of your profile and balances from Settings → Your
          data. To request deletion, contact support; the audit trail of operator
          actions is retained, but your profile and balances are removed.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">Retention</h2>
        <p className="mt-2">
          Account and transaction records are kept for as long as your account is
          open and for the period afterwards required by applicable record-keeping
          rules. Audit records of privileged actions are retained.
        </p>
      </section>
    </LegalLayout>
  );
}
