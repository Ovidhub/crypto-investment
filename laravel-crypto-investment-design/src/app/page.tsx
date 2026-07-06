import { db } from "@/db";
import { waitlist } from "@/db/schema";
import { sql } from "drizzle-orm";
import { fetchLiveCoins } from "@/lib/coins";
import { getCurrentUser } from "@/lib/auth";
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

export const dynamic = "force-dynamic";

async function getWaitlistTotal(): Promise<number> {
  try {
    const [row] = await db
      .select({ count: sql<number>`count(*)::int` })
      .from(waitlist);
    return row?.count ?? 0;
  } catch {
    return 0;
  }
}

export default async function HomePage() {
  const [coins, total, me] = await Promise.all([
    fetchLiveCoins(),
    getWaitlistTotal(),
    getCurrentUser(),
  ]);

  const baseInvestors = 5218 + total;

  return (
    <>
      <Navbar loggedIn={!!me} />
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
        <JoinCta initialTotal={baseInvestors} />
      </main>
      <Footer />
    </>
  );
}
