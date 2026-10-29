import Image from "next/image";

const BULLETS = [
  {
    title: "Every number is traceable",
    text: "Balances come from the ledger, not from a column someone edited. The same journal that credits your wallet updates the platform's books.",
  },
  {
    title: "Live market data, honest labels",
    text: "Prices stream from CoinGecko. Anything the platform calculates on top of them is booked in the ledger.",
  },
  {
    title: "Guardrails built in",
    text: "Deposits and withdrawals require operator approval, withdrawals reserve funds first, and the accrual job is idempotent per day.",
  },
];

export default function Insights() {
  return (
    <section id="insights" className="relative overflow-hidden py-20">
      <div className="pointer-events-none absolute left-1/2 top-1/2 -z-10 h-[420px] w-[720px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-accent/10 blur-[130px]" />
      <div className="mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-2">
        <div className="relative order-2 lg:order-1">
          <div className="relative mx-auto aspect-[4/5] w-full max-w-[440px]">
            <Image
              src="/images/app-mockup.png"
              alt="Enethi crypto app dashboard"
              fill
              className="object-contain drop-shadow-[0_30px_60px_rgba(34,229,138,0.25)]"
              sizes="(max-width: 1024px) 90vw, 440px"
            />
          </div>
        </div>

        <div className="order-1 lg:order-2">
          <p className="text-sm font-semibold uppercase tracking-[0.18em] text-accent">
            Built like the real thing
          </p>
          <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
            The plumbing that makes a platform trustworthy
          </h2>
          <p className="mt-4 max-w-md text-sm leading-relaxed text-[#9aa3b2]">
            Every balance is booked, not assumed: two-sided journal
            entries, reserved withdrawal funds, approval workflows and an
            append-only audit trail.
          </p>

          <div className="mt-8 space-y-5">
            {BULLETS.map((b, i) => (
              <div key={b.title} className="flex gap-4">
                <span className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand to-brand-2 font-display text-sm font-bold text-white">
                  {i + 1}
                </span>
                <div>
                  <h3 className="font-semibold text-white">{b.title}</h3>
                  <p className="mt-1 text-sm text-[#9aa3b2]">{b.text}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
