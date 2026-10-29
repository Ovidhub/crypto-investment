/**
 * Live crypto market headlines.
 *
 * Pulls public RSS feeds from three outlets, parses the few fields the page
 * needs and merges them newest-first. No API key, no third-party SDK, and no
 * invented content: if every feed is unreachable the page shows an explicit
 * "unavailable" note rather than filler headlines.
 */

export type NewsItem = {
  title: string;
  link: string;
  source: string;
  publishedAt: string | null;
};

type Feed = { name: string; url: string; home: string };

const FEEDS: Feed[] = [
  { name: "CoinDesk", url: "https://www.coindesk.com/arc/outboundfeeds/rss/", home: "https://www.coindesk.com" },
  { name: "Cointelegraph", url: "https://cointelegraph.com/rss", home: "https://cointelegraph.com" },
  { name: "Decrypt", url: "https://decrypt.co/feed", home: "https://decrypt.co" },
];

/** Feeds are cached for five minutes so a busy dashboard does not hammer them. */
const REVALIDATE_SECONDS = 300;
const TIMEOUT_MS = 6_000;

function decodeEntities(value: string): string {
  return value
    .replace(/<!\[CDATA\[([\s\S]*?)\]\]>/g, "$1")
    .replace(/&#(\d+);/g, (_, code: string) => String.fromCharCode(Number(code)))
    .replace(/&#x([0-9a-fA-F]+);/g, (_, code: string) => String.fromCharCode(parseInt(code, 16)))
    .replace(/&quot;/g, '"')
    .replace(/&apos;/g, "'")
    .replace(/&nbsp;/g, " ")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&amp;/g, "&")
    .replace(/<[^>]+>/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

function tag(block: string, name: string): string {
  const match = block.match(new RegExp(`<${name}[^>]*>([\\s\\S]*?)</${name}>`, "i"));
  return match ? decodeEntities(match[1]) : "";
}

/** Absolute link, so a relative href in a feed cannot break the anchor. */
function absolute(link: string, home: string): string | null {
  if (!link) return null;
  try {
    const url = new URL(link, home);
    if (url.protocol !== "https:" && url.protocol !== "http:") return null;
    return url.toString();
  } catch {
    return null;
  }
}

function parseRss(xml: string, feed: Feed): NewsItem[] {
  const items: NewsItem[] = [];
  const blocks = xml.match(/<item[\s\S]*?<\/item>/gi) ?? [];

  for (const block of blocks) {
    const title = tag(block, "title");
    if (!title) continue;
    const link = absolute(
      tag(block, "link") || tag(block, "guid"),
      feed.home,
    );
    if (!link) continue;

    const rawDate = tag(block, "pubDate") || tag(block, "published") || "";
    const parsed = rawDate ? new Date(rawDate) : null;
    const publishedAt =
      parsed && !Number.isNaN(parsed.getTime()) ? parsed.toISOString() : null;

    items.push({ title: title.slice(0, 240), link, source: feed.name, publishedAt });
  }

  return items;
}

async function loadFeed(feed: Feed): Promise<NewsItem[]> {
  try {
    const response = await fetch(feed.url, {
      headers: {
        accept: "application/rss+xml, application/xml, text/xml;q=0.9, */*;q=0.8",
        "user-agent":
          "Mozilla/5.0 (compatible; ActuatradesBot/1.0; +https://actuatrades.com)",
      },
      next: { revalidate: REVALIDATE_SECONDS },
      signal: AbortSignal.timeout(TIMEOUT_MS),
    });
    if (!response.ok) return [];
    return parseRss(await response.text(), feed);
  } catch {
    // One dead feed must not take the section down.
    return [];
  }
}

/** Newest-first headlines from whichever feeds answered. */
export async function fetchMarketNews(limit = 8): Promise<NewsItem[]> {
  const results = await Promise.all(FEEDS.map(loadFeed));
  const all = results.flat();

  // The same story is often syndicated, so de-duplicate on the title.
  const seen = new Set<string>();
  const unique: NewsItem[] = [];
  for (const item of all) {
    const key = item.title.toLowerCase().slice(0, 90);
    if (seen.has(key)) continue;
    seen.add(key);
    unique.push(item);
  }

  unique.sort((a, b) => {
    const aTime = a.publishedAt ? Date.parse(a.publishedAt) : 0;
    const bTime = b.publishedAt ? Date.parse(b.publishedAt) : 0;
    return bTime - aTime;
  });

  return unique.slice(0, limit);
}

/** Compact "2h ago" label; computed on the server so hydration stays stable. */
export function timeAgo(iso: string | null): string {
  if (!iso) return "";
  const ms = Date.now() - Date.parse(iso);
  if (!Number.isFinite(ms) || ms < 0) return "";
  const minutes = Math.round(ms / 60_000);
  if (minutes < 1) return "just now";
  if (minutes < 60) return `${minutes}m ago`;
  const hours = Math.round(minutes / 60);
  if (hours < 24) return `${hours}h ago`;
  const days = Math.round(hours / 24);
  return `${days}d ago`;
}
