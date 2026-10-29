import { copy, getSiteContent } from "@/lib/content";
import BrandMark from "@/components/BrandMark";

const COLUMNS = [
  {
    title: "My Account",
    links: [
      { label: "Dashboard", href: "/dashboard" },
      { label: "Wallet", href: "/dashboard/wallet" },
      { label: "Trade", href: "/dashboard/markets" },
      { label: "Sign in", href: "/login" },
    ],
  },
  {
    title: "Resources",
    links: [
      { label: "Pricing", href: "/pricing" },
      { label: "Contact", href: "/contact" },
      { label: "Markets", href: "/#markets" },
      { label: "FAQ", href: "/pricing#faq" },
    ],
  },
  {
    title: "Company",
    links: [
      { label: "About", href: "/about" },
      { label: "Careers", href: "/about" },
      { label: "Blog", href: "/about" },
      { label: "Press", href: "/about" },
    ],
  },
];

export default async function Footer() {
  const c = await getSiteContent();
  return (
    <footer className="border-t border-line bg-panel">
      <div className="mx-auto max-w-7xl px-5 py-14 sm:px-8">
        <div className="grid gap-10 md:grid-cols-[1.5fr_1fr_1fr_1fr]">
          <div>
            <a href="#top" className="flex items-center gap-2.5">
              <BrandMark size={30} />
              <span className="font-display text-lg font-bold">
                {copy(c, "brand.name")}
              </span>
            </a>
            <p className="mt-4 max-w-xs text-sm leading-relaxed text-[#9aa3b2]">
              {copy(c, "footer.about")}
            </p>
            <div className="mt-5 flex gap-3">
              {["𝕏", "in", "f", "▶"].map((s) => (
                <a
                  key={s}
                  href="#"
                  className="grid h-9 w-9 place-items-center rounded-full border border-line bg-card text-sm text-[#c7cedb] transition-colors hover:border-brand hover:text-white"
                >
                  {s}
                </a>
              ))}
            </div>
          </div>

          {COLUMNS.map((col) => (
            <div key={col.title}>
              <h4 className="font-display font-semibold text-white">
                {col.title}
              </h4>
              <ul className="mt-4 space-y-2.5">
                {col.links.map((l) => (
                  <li key={l.label}>
                    <a
                      href={l.href}
                      className="text-sm text-[#9aa3b2] transition-colors hover:text-white"
                    >
                      {l.label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t border-line pt-6 sm:flex-row">
          <p className="text-xs text-[#6b7488]">
            {copy(c, "footer.copyright")}
          </p>
          <p className="text-xs text-[#6b7488]">
            Crypto investing involves risk. Invest responsibly.
          </p>
        </div>
      </div>
    </footer>
  );
}
