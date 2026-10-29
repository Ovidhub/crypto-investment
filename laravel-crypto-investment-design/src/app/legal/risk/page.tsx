import LegalLayout from "@/components/LegalLayout";
import Link from "next/link";

export const dynamic = "force-dynamic";

export default function RiskPage() {
  return (
    <LegalLayout title="Risk disclosure" updated="28 October 2026">
      <section className="rounded-2xl border border-[#f5a524]/40 bg-[#f5a524]/10 p-5 text-sm text-[#e9d3a8]">
        <p className="font-semibold text-white">Short version</p>
        <p className="mt-2">
          Crypto assets are volatile. You can lose part or all of what you put in.
          Target rates are goals, not promises, and no return is guaranteed.
          Nothing on this site is financial advice.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">1. Capital at risk</h2>
        <p className="mt-2">
          Balances shown in your wallet are held in your account and move only when
          you transact. Market movements, strategy performance and settlement of a
          trade can reduce the value of your holdings, including the amount you
          originally committed.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">2. No returns are promised</h2>
        <p className="mt-2">
          A plan&apos;s target rate is an objective, not a guarantee. Daily credits
          are calculated from the rate you subscribed at and are funded from plan
          performance; they can be lower than the target, paused, or stopped if the
          position moves against us.
        </p>
        <p className="mt-2">
          Real crypto assets are volatile and can lose most or all of their value.
          Past performance does not predict future results.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">3. Fixed-odds trades</h2>
        <p className="mt-2">
          Trades on the desk settle against the market at expiry. If the market moves
          against your position, the stake you committed is lost. Only commit an
          amount you are prepared to lose in full.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">4. Not financial advice</h2>
        <p className="mt-2">
          Nothing on this site is a recommendation, solicitation or offer to buy or
          sell any asset. Speak to a licensed professional in your jurisdiction
          before investing.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">5. Data and availability</h2>
        <p className="mt-2">
          Service may be interrupted for maintenance or by upstream outages. Keep
          your own record of any transaction you need for tax or accounting purposes.
        </p>
      </section>

      <section>
        <h2 className="font-display text-lg font-bold text-white">6. Market data</h2>
        <p className="mt-2">
          Prices come from the public CoinGecko API and may be delayed or
          unavailable. When the API cannot be reached the application marks the
          prices it shows as indicative so the interface remains usable.
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
