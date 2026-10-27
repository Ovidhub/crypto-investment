import LegalLayout from "@/components/LegalLayout";

export const dynamic = "force-dynamic";

export default function PrivacyPage() {
  return (
    <LegalLayout title="Privacy notice" updated="28 October 2026">
      <section>
        <h2 className="font-display text-lg font-bold text-white">What we store</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>Your name, email address and a scrypt hash of your password.</li>
          <li>Ledger entries and positions created by your activity in the demo.</li>
          <li>A signed session cookie so you stay signed in. It contains no password.</li>
          <li>
            For the verification demo: your name, country, document type and the
            last four digits you type. No images, no full document numbers.
          </li>
          <li>Audit records for privileged actions, including the actor&apos;s email.</li>
        </ul>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">What we do not do</h2>
        <ul className="mt-2 list-disc space-y-1.5 pl-5">
          <li>No payment details are collected, because there is no payment rail.</li>
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
          You can download a JSON export of your profile and balances from
          Settings → Your data. To request deletion, contact support; the audit
          trail of operator actions is retained, but your profile and balances are
          removed.
        </p>
      </section>
      <section>
        <h2 className="font-display text-lg font-bold text-white">Retention</h2>
        <p className="mt-2">
          In the default demo configuration the database lives in server memory and
          is cleared whenever the process restarts. Where a persistent database is
          configured, data is retained for the life of the demonstration.
        </p>
      </section>
    </LegalLayout>
  );
}
