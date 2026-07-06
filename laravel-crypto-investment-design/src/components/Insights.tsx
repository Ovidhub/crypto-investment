import Image from "next/image";

const BULLETS = [
  {
    title: "Track your earnings easily",
    text: "Real-time P&L, allocation breakdowns and performance history in one clean dashboard.",
  },
  {
    title: "Detailed market insights",
    text: "AI-driven signals, sentiment analysis and on-chain data to guide smarter decisions.",
  },
  {
    title: "Automated passive income",
    text: "Earn up to 12% APY with staking and yield vaults that compound automatically.",
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
            Get more detailed insights
          </p>
          <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
            Track your earnings more easily &amp; more fast
          </h2>
          <p className="mt-4 max-w-md text-sm leading-relaxed text-[#9aa3b2]">
            A powerful mobile experience that puts the whole market in your
            pocket. Monitor every position, react instantly and let your money
            work while you sleep.
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
