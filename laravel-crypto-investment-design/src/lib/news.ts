/**
 * Live crypto market headlines.
 *
 * Pulls public RSS feeds from three outlets, parses the few fields the page
 * needs and merges them newest-first. No API key, no third-party SDK, and no
 * invented content: if every feed is unreachable the page shows an explicit
 * "unavailable" note rather than filler headlines.
 *
 * Most feeds ship a lead image inside the item (`media:content`, `media:thumbnail`
 * or an `<img>` in the article body). The ones that do not get a single
 * `og:image` lookup on the article page, so a card is never left with a hole.
 */

export type NewsItem = {
  title: string;
  link: string;
  source: string;
  publishedAt: string | null;
  image: string | null;
  category: string | null;
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
/** Article pages change slowly, so their lead image is cached for an hour. */
const ARTICLE_REVALIDATE_SECONDS = 3_600;
const ARTICLE_TIMEOUT_MS = 4_000;

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

/** Value of an attribute on the first matching tag, e.g. `media:content` `url`. */
function attribute(block: string, tagName: string, attrName: string): string {
  const match = block.match(
    new RegExp(`<${tagName}\\b[^>]*\\b${attrName}\\s*=\\s*["']([^"']+)["']`, "i"),
  );
  return match ? match[1].replace(/&amp;/g, "&").trim() : "";
}

function isUsableImage(value: string): boolean {
  if (!value) return false;
  if (!/^https?:\/\//i.test(value)) return false;
  // Tracking pixels and spacer gifs are not lead images.
  return !/(1x1|spacer|pixel\.gif|blank\.gif)/i.test(value);
}

/**
 * Lead image from the item itself, in order of preference. Feeds are
 * inconsistent here, so try every shape they commonly use.
 */
function itemImage(block: string): string | null {
  const candidates = [
    attribute(block, "media:content", "url"),
    attribute(block, "media:thumbnail", "url"),
    attribute(block, "enclosure", "url"),
  ];
  for (const candidate of candidates) {
    if (isUsableImage(candidate)) return candidate;
  }

  // Some feeds only embed the picture in the article body.
  const body = block.match(/<img\b[^>]*\bsrc\s*=\s*["']([^"']+)["']/i);
  const inline = body ? body[1].replace(/&amp;/g, "&").trim() : "";
  return isUsableImage(inline) ? inline : null;
}

/** First feed category, used as the small label above a headline. */
function itemCategory(block: string): string | null {
  const value = tag(block, "category");
  if (!value) return null;
  const clean = value.replace(/\s+/g, " ").trim();
  if (!clean || clean.length > 28) return null;
  return clean;
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

    items.push({
      title: title.slice(0, 240),
      link,
      source: feed.name,
      publishedAt,
      image: itemImage(block),
      category: itemCategory(block),
    });
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

/** `og:image` from the article page, for feeds that ship no image at all. */
async function articleImage(link: string): Promise<string | null> {
  try {
    const response = await fetch(link, {
      headers: {
        accept: "text/html",
        "user-agent":
          "Mozilla/5.0 (compatible; ActuatradesBot/1.0; +https://actuatrades.com)",
      },
      next: { revalidate: ARTICLE_REVALIDATE_SECONDS },
      signal: AbortSignal.timeout(ARTICLE_TIMEOUT_MS),
    });
    if (!response.ok) return null;
    // Only the <head> matters; reading the whole document keeps the parser simple.
    const html = await response.text();
    const head = html.slice(0, 40_000);
    const match =
      head.match(/<meta[^>]+property=["']og:image(?::secure_url)?["'][^>]+content=["']([^"']+)["']/i) ??
      head.match(/<meta[^>]+content=["']([^"']+)["'][^>]+property=["']og:image(?::secure_url)?["']/i) ??
      head.match(/<meta[^>]+name=["']twitter:image["'][^>]+content=["']([^"']+)["']/i);
    const value = match ? match[1].replace(/&amp;/g, "&").trim() : "";
    return isUsableImage(value) ? value : null;
  } catch {
    return null;
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

  const selected = unique.slice(0, limit);

  // Cap the extra page loads; the newest few are the ones people actually see.
  const needImage = selected.filter((item) => !item.image).slice(0, 8);
  if (needImage.length > 0) {
    const found = await Promise.all(needImage.map((item) => articleImage(item.link)));
    const byLink = new Map(needImage.map((item, index) => [item.link, found[index]]));
    for (const item of selected) {
      if (!item.image) item.image = byLink.get(item.link) ?? null;
    }
  }

  return selected;
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
