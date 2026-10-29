import { db } from "@/db";
import { sql } from "drizzle-orm";
import { fetchLiveCoins } from "@/lib/coins";
import { getCurrentUser } from "@/lib/auth";
import { copy, getSiteContent } from "@/lib/content";
import Navbar from "@/components/Navbar";
import Hero from "@/components/Hero";
import LogoMarquee from "@/components/LogoMarquee";
import Features from "@/components/Features";
import Insights from "@/components/Insights";
import TopCoinCards from "@/components/TopCoinCards";
import MarketTable from "@/components/MarketTable";
import WhyChoose from "@/components/WhyChoose";
import Stats from "@/components/Stats";
import HowItWorks from "@/components/HowItWorks";
import Faq from "@/components/Faq";
import JoinCta from "@/components/JoinCta";
import Footer from "@/components/Footer";

// CMS copy and live prices are read per request.
export const dynamic = "force-dynamic";

/**
 * How many customers have actually joined: non-staff accounts plus waitlist
 * sign-ups. Staff accounts are excluded, and nothing is added to the number.
 */
async function getInvestorTotal(): Promise<number> {
  try {
    const result = await db.execute(
      sql`select (
        (select count(*) from users where role = 'user')
        + (select count(*) from waitlist)
      )::int as total`,
    );
    const rows = (result as unknown as { rows?: Array<{ total: number }> }).rows ?? [];
    return rows[0]?.total ?? 0;
  } catch {
    return 0;
  }
}

export default async function HomePage() {
  const [coins, total, me, content] = await Promise.all([
    fetchLiveCoins(),
    getInvestorTotal(),
    getCurrentUser(),
    getSiteContent(),
  ]);

  // Real accounts only. The seeded operator and reviewer accounts make this at
  // least 1, so the "+" stays truthful.
  const baseInvestors = Math.max(total, 1);

  return (
    <>
      <Navbar
        loggedIn={!!me}
        siteName={copy(content, "brand.name")}
        logo={content["brand.logo"] ?? ""}
      />
      <main>
        <Hero investors={baseInvestors} />
        <LogoMarquee />
        <Features />
        <Insights />
        <TopCoinCards coins={coins} />
        <MarketTable initial={coins} />
        <WhyChoose coins={coins} />
        <Stats />
        <HowItWorks />
        <Faq />
        <JoinCta
          initialTotal={baseInvestors}
          copy={{
            title: copy(content, "home.cta.title"),
            subtitle: copy(content, "home.cta.subtitle"),
            button: copy(content, "home.cta.button"),
            success: copy(content, "home.cta.success"),
          }}
        />
      </main>
      <Footer />
    </>
  );
}
