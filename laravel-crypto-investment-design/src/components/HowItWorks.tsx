import { copy, getSiteContent } from "@/lib/content";

export default async function HowItWorks() {
  const c = await getSiteContent();
  const STEPS = [1, 2, 3].map((n) => ({
    step: String(n).padStart(2, "0"),
    title: copy(c, `home.steps.item${n}.title`),
    text: copy(c, `home.steps.item${n}.text`),
  }));
  return (
    <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8">
      <div className="mb-10 text-center">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-accent">
          {copy(c, "home.steps.eyebrow")}
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
          {copy(c, "home.steps.title")}
        </h2>
      </div>

      <div className="grid gap-5 md:grid-cols-3">
        {STEPS.map((s) => (
          <div
            key={s.step}
            className="relative overflow-hidden rounded-3xl border border-line bg-card p-8"
          >
            <span className="font-display text-6xl font-bold text-white/5">
              {s.step}
            </span>
            <h3 className="mt-2 font-display text-xl font-bold">{s.title}</h3>
            <p className="mt-3 text-sm leading-relaxed text-[#9aa3b2]">
              {s.text}
            </p>
          </div>
        ))}
      </div>
    </section>
  );
}
