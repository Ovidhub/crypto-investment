import { NextResponse } from "next/server";
import { fetchMarketNews } from "@/lib/news";
import { DEMO_ACCOUNT } from "@/lib/demo-access";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/** TEMPORARY: proves the signed-in pages render the new sections. */
export async function GET() {
  const origin = "https://actuatrades.com";
  const out: Record<string, unknown> = {};

  const news = await fetchMarketNews(8);
  out.news = {
    count: news.length,
    first: news[0] ? `${news[0].source}: ${news[0].title.slice(0, 80)}` : null,
    sources: [...new Set(news.map((n) => n.source))],
  };

  const login = await fetch(`${origin}/api/auth/login`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({
      email: DEMO_ACCOUNT.email,
      password: DEMO_ACCOUNT.password,
    }),
  });
  out.login = { status: login.status, email: DEMO_ACCOUNT.email };
  const cookie = login.headers.get("set-cookie")?.split(";")[0] ?? "";

  for (const path of ["/dashboard", "/dashboard/markets"]) {
    const page = await fetch(`${origin}${path}`, { headers: { cookie } });
    const html = await page.text();
    out[path] = {
      status: page.status,
      marketNews: html.includes("Market news"),
      quickTrade: html.includes("Quick trade"),
      openPositions: html.includes("Open positions"),
      chart: html.includes("Live price history"),
      simTicket: html.includes("Take a position"),
    };
  }

  const home = await fetch(origin);
  const homeHtml = await home.text();
  out.brand = {
    status: home.status,
    actua: homeHtml.includes("Actuatrades"),
    enethiLeft: /enethi/i.test(homeHtml),
  };

  return NextResponse.json(out);
}
