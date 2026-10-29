import { fetchMarketNews, timeAgo } from "@/lib/news";

/**
 * Headlines from public crypto news RSS feeds. Rendered on the dashboard so a
 * signed-in customer sees what is moving the market next to their balances.
 */
export default async function MarketNews({ limit = 7 }: { limit?: number }) {
  const items = await fetchMarketNews(limit);

  return (
    <div className="rounded-2xl border border-line bg-card">
      <div className="flex items-center justify-between border-b border-line px-6 py-4">
        <h2 className="font-display font-bold">Market news</h2>
        <span className="text-[11px] uppercase tracking-wide text-[#7c8698]">Live feed</span>
      </div>

      {items.length === 0 ? (
        <p className="px-6 py-5 text-sm text-[#9aa3b2]">
          Headlines are unavailable right now. The news feeds did not respond — this
          panel fills in automatically once they do.
        </p>
      ) : (
        <ul className="divide-y divide-line/60">
          {items.map((item) => (
            <li key={item.link} className="px-6 py-3.5">
              <a
                href={item.link}
                target="_blank"
                rel="noopener noreferrer nofollow"
                className="text-sm font-semibold text-white hover:text-brand"
              >
                {item.title}
              </a>
              <p className="mt-1 text-[11px] text-[#7c8698]">
                {item.source}
                {item.publishedAt ? ` · ${timeAgo(item.publishedAt)}` : ""}
              </p>
            </li>
          ))}
        </ul>
      )}

      <p className="border-t border-line px-6 py-3 text-[11px] text-[#7c8698]">
        Headlines and links belong to the publishers. Actuatrades does not endorse
        them and they are not investment advice.
      </p>
    </div>
  );
}
