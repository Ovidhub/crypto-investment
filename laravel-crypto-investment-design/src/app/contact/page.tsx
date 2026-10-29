import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import ContactForm from "@/components/ContactForm";
import { getCurrentUser } from "@/lib/auth";
import { copy, getSiteContent } from "@/lib/content";

export const dynamic = "force-dynamic";

const CHANNEL_ICONS = ["✉️", "💬", "🏢"];

export default async function ContactPage() {
  const [me, content] = await Promise.all([getCurrentUser(), getSiteContent()]);
  return (
    <>
      <Navbar
        loggedIn={!!me}
        siteName={copy(content, "brand.name")}
        logo={content["brand.logo"] ?? ""}
      />
      <main>
        <section className="relative overflow-hidden px-5 pb-16 pt-36 sm:px-8">
          <div className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 grid-bg" />
            <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
          </div>
          <div className="mx-auto grid max-w-6xl items-start gap-12 lg:grid-cols-2">
            <div>
              <span className="inline-flex items-center gap-2 rounded-full border border-line bg-white/5 px-4 py-1.5 text-xs font-medium text-[#c7cedb]">
                Contact
              </span>
              <h1 className="mt-6 font-display text-[clamp(2.4rem,5vw,3.6rem)] font-bold leading-[1.03]">
                Let&apos;s <span className="brand-gradient">talk</span>
              </h1>
              <p className="mt-5 max-w-md text-base text-[#9aa3b2]">
                {copy(content, "contact.intro")}
              </p>

              <div className="mt-8 space-y-4">
                {[
                  { icon: CHANNEL_ICONS[0], label: "Email us", value: copy(content, "contact.email") },
                  { icon: CHANNEL_ICONS[1], label: "Live chat", value: copy(content, "contact.chat") },
                  { icon: CHANNEL_ICONS[2], label: "Headquarters", value: copy(content, "contact.office") },
                ].map((c) => (
                  <div
                    key={c.label}
                    className="flex items-center gap-4 rounded-2xl border border-line bg-card p-4"
                  >
                    <span className="grid h-11 w-11 place-items-center rounded-xl bg-panel-2 text-lg">
                      {c.icon}
                    </span>
                    <div>
                      <p className="text-xs text-[#7c8698]">{c.label}</p>
                      <p className="font-semibold text-white">{c.value}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <ContactForm />
          </div>
        </section>
      </main>
      <Footer />
    </>
  );
}
