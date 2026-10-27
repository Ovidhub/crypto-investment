import LegalLayout from "@/components/LegalLayout";
import Link from "next/link";

export const dynamic = "force-dynamic";

export default function RiskPage() {
  return (
    <LegalLayout title="Risk disclosure" updated="28 October 2026">
      <section className="rounded-2xl border border-[#f5a524]/40 bg-[#f5a524]/10 p-5 text-sm text-[#e9d3a8]">
        <p className="font-semibold text-white">Short version</p>
        <p className="mt-2">
          Enethi is a software demonstration. Every balance is virtual. We do not
          accept deposits, hold assets, execute trades on any exchange, or pay
          withdrawals. Nothing here is an investment, a financial product, or
          advice.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">1. Simulated funds</h2>
        <p className="mt-2">
          New accounts receive a simulated credit so the interface can be used.
          Deposits, withdrawals, trades, plan accruals and referral bonuses all
          change entries in a database only. No bank, card network, wallet or
          blockchain is involved at any point.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">2. No returns are promised</h2>
        <p className="mt-2">
          Plan rates are inputs to our own simulator. The daily credit is
          arithmetic on that rate, funded by a simulated expense line in the
          ledger. It is not a market yield, a fund return, an APY offer, or an
          indication of what any real strategy would produce.
        </p>
        <p className="mt-2">
          Real crypto assets are volatile and can lose most or all of their
          value. Past performance and simulated performance do not predict future
          results.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">3. Not financial advice</h2>
        <p className="mt-2">
          Nothing on this site is a recommendation, solicitation or offer to buy
          or sell any asset. If you are considering investing real money, speak to
          a licensed professional in your jurisdiction.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">4. Data and availability</h2>
        <p className="mt-2">
          By default this deployment keeps data in process memory, which resets on
          restart. Accounts created in the demo may disappear without notice. Do
          not store personal or sensitive information, including real identity
          documents, in this application.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">5. Market data</h2>
        <p className="mt-2">
          Prices come from the public CoinGecko API and may be delayed or
          unavailable. When the API cannot be reached the application shows
          clearly-labelled sample prices so the interface remains usable.
        </p>
        <p className="mt-4">
          See also the{" "}
          <Link href="/legal/terms" className="font-semibold text-brand hover:underline">
            terms of use
          </Link>{" "}
          and{" "}
          <Link href="/legal/privacy" className="font-semibold text-brand hover:underline">
            privacy notice
          </Link>
          .
        </p>
      </section>
    </LegalLayout>
  );
}
