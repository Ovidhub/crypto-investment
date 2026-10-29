import { copy, getSiteContent } from "@/lib/content";

const ICONS = ["◈", "◷", "▤"];

export default async function Features() {
  const c = await getSiteContent();
  const features = [1, 2, 3].map((n, i) => ({
    icon: ICONS[i],
    title: copy(c, `home.features.item${n}.title`),
    text: copy(c, `home.features.item${n}.text`),
  }));
  return (
    <section id="features" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="mb-12 max-w-2xl">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
          {copy(c, "home.features.eyebrow")}
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold leading-tight">
          {copy(c, "home.features.title")}
        </h2>
        <p className="mt-4 text-sm leading-relaxed text-[#9aa3b2]">
          {copy(c, "home.features.intro")}
        </p>
      </div>

      <div className="grid gap-5 md:grid-cols-3">
        {features.map((f) => (
          <div
            key={f.title}
            className="group relative overflow-hidden rounded-3xl border border-line bg-card p-7 transition-all hover:-translate-y-1 hover:border-brand/40"
          >
            <div className="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand/10 blur-2xl transition-opacity group-hover:opacity-100 sm:opacity-0" />
            <div className="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-panel-2 text-2xl text-brand">
              {f.icon}
            </div>
            <h3 className="mt-5 font-display text-xl font-bold">{f.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">{f.text}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
