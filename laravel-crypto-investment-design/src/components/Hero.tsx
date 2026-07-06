import Image from "next/image";

export default function Hero({ investors }: { investors: number }) {
  return (
    <section id="top" className="relative overflow-hidden pt-28 pb-16 sm:pt-36">
      {/* background glow + grid */}
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute inset-0 grid-bg" />
        <div className="absolute -top-40 left-1/2 h-[520px] w-[820px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
        <div className="absolute right-0 top-40 h-[380px] w-[380px] rounded-full bg-accent/15 blur-[120px]" />
      </div>

      <div className="mx-auto grid max-w-7xl items-center gap-10 px-5 sm:px-8 lg:grid-cols-2 lg:gap-6">
        <div className="animate-fade-up">
          <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
            <span className="h-1.5 w-1.5 rounded-full bg-accent" />
            Crypto investing platform of the future
          </span>

          <h1 className="mt-6 font-display text-[clamp(2.6rem,6vw,4.6rem)] font-bold leading-[0.98] tracking-tight">
            Invest In <span className="brand-gradient">Crypto</span> Earn Easy
            Passive <span className="brand-gradient">Money</span> Easily
          </h1>

          <p className="mt-6 max-w-md text-base leading-relaxed text-[#9aa3b2]">
            Enethi makes it simple to buy, sell and grow 99+ digital assets.
            Bank-grade security, real-time market data and a portfolio that works
            for you around the clock.
          </p>

          <div className="mt-8 flex flex-wrap items-center gap-4">
            <a
              href="#join"
              className="rounded-full bg-gradient-to-r from-brand to-brand-2 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-2/40 transition-transform hover:scale-[1.03]"
            >
              Start Investing
            </a>
            <a
              href="#how"
              className="flex items-center gap-2 rounded-full border border-line bg-white/5 px-6 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-white/10"
            >
              <span className="grid h-6 w-6 place-items-center rounded-full bg-white text-ink">
                ▶
              </span>
              How It Works?
            </a>
          </div>

          <div className="mt-10 flex items-center gap-6">
            <div className="flex -space-x-3">
              {["#f7931a", "#627eea", "#14f195", "#e6007a"].map((c, i) => (
                <span
                  key={i}
                  className="h-9 w-9 rounded-full border-2 border-ink"
                  style={{ background: `linear-gradient(135deg,${c},${c}aa)` }}
                />
              ))}
            </div>
            <div>
              <p className="font-display text-lg font-bold text-white">
                {investors.toLocaleString()}+
              </p>
              <p className="text-xs text-[#9aa3b2]">investors already onboard</p>
            </div>
          </div>
        </div>

        {/* Visual */}
        <div className="relative animate-fade-up [animation-delay:120ms]">
          <div className="relative mx-auto aspect-square w-full max-w-[520px]">
            <div className="absolute inset-0 animate-spin-slow rounded-full border border-brand/20" />
            <div className="absolute inset-10 rounded-full border border-white/5" />
            <Image
              src="/images/hero-robot.png"
              alt="AI powered crypto investing network"
              fill
              priority
              className="animate-float object-contain drop-shadow-[0_30px_60px_rgba(124,58,237,0.35)]"
              sizes="(max-width: 1024px) 90vw, 520px"
            />
          </div>

          {/* floating price chips */}
          <div className="absolute left-0 top-10 hidden rounded-2xl border border-line glass px-4 py-3 sm:block">
            <p className="text-[10px] text-[#9aa3b2]">BTC / USD</p>
            <p className="font-display text-lg font-bold text-white">$83,411</p>
            <p className="text-xs font-semibold text-accent">+1.77%</p>
          </div>
          <div className="absolute bottom-6 right-0 hidden rounded-2xl border border-line glass px-4 py-3 sm:block">
            <p className="text-[10px] text-[#9aa3b2]">Portfolio</p>
            <p className="font-display text-lg font-bold text-white">+24.6%</p>
            <p className="text-xs font-semibold text-accent">this month</p>
          </div>
        </div>
      </div>
    </section>
  );
}
