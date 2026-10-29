import { copy, getSiteContent } from "@/lib/content";

export default async function LogoMarquee() {
  const c = await getSiteContent();
  const items = copy(c, "home.marquee.items")
    .split(",")
    .map((name) => name.trim())
    .filter(Boolean)
    .slice(0, 12);

  if (items.length === 0) return null;

  return (
    <section className="relative border-y border-line bg-gradient-to-r from-brand-2/20 via-brand/10 to-accent/10 py-6">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <p className="mb-4 text-center text-xs uppercase tracking-[0.2em] text-[#c7cedb]">
          {copy(c, "home.marquee.label")}
        </p>
        <div className="no-scrollbar overflow-hidden">
          <div className="flex w-max animate-marquee items-center gap-14">
            {[...items, ...items].map((name, i) => (
              <span
                key={i}
                className="whitespace-nowrap font-display text-xl font-bold text-white/70 transition-colors hover:text-white"
              >
                {name}
              </span>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
