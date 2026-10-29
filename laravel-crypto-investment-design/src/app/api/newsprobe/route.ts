import { NextResponse } from "next/server";
import { fetchMarketNews } from "@/lib/news";
import { DEMO_ACCOUNT } from "@/lib/demo-access";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/** TEMPORARY: proves the news cards carry images and the overview shows a chart. */
export async function GET() {
  const origin = "https://actuatrades.com";
  const items = await fetchMarketNews(6);

  const out: Record<string, unknown> = {
    items: items.map((i) => ({
      source: i.source,
      cat: i.category,
      img: i.image ? i.image.slice(0, 90) : null,
      title: i.title.slice(0, 55),
    })),
    withImage: items.filter((i) => i.image).length,
    total: items.length,
  };

  const login = await fetch(`${origin}/api/auth/login`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ email: DEMO_ACCOUNT.email, password: DEMO_ACCOUNT.password }),
  });
  const cookie = login.headers.get("set-cookie")?.split(";")[0] ?? "";
  const page = await fetch(`${origin}/dashboard`, { headers: { cookie } });
  const html = await page.text();
  out.dashboard = {
    login: login.status,
    page: page.status,
    htmlImages: (html.match(/<img /g) ?? []).length,
    newsblock: html.includes("Live from the newsroom"),
    chart: html.includes("Live chart"),
    height190: html.includes("height:190px"),
  };

  return NextResponse.json(out);
}
