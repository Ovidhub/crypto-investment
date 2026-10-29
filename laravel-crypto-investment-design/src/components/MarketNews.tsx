import { fetchMarketNews, timeAgo } from "@/lib/news";
import NewsThumb from "@/components/NewsThumb";

/**
 * Headlines from public crypto news RSS feeds. Rendered on the dashboard so a
 * signed-in customer sees what is moving the market next to their balances.
 *
 * Cards carry the publisher's lead image where the feed supplies one; the
 * publishers' own categories are shown as labels instead of invented ones.
 */
export default async function MarketNews({
  limit = 6,
  columns = 2,
}: {
  limit?: number;
  columns?: 1 | 2;
}) {
  const items = await fetchMarketNews(limit);

  return (
    <div className="rounded-2xl border border-line bg-card">
      <div className="flex items-center justify-between gap-3 border-b border-line px-6 py-4">
        <div>
          <h2 className="font-display font-bold">Market news</h2>
          <p className="mt-0.5 text-xs text-[#7c8698]">
            Live from the newsroom — tap a headline to read the full story.
          </p>
        </div>
        <span className="flex shrink-0 items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wide text-[#7c8698]">
          <span className="h-1.5 w-1.5 rounded-full bg-accent" />
          Live feed
        </span>
      </div>

      {items.length === 0 ? (
        <p className="px-6 py-5 text-sm text-[#9aa3b2]">
          Headlines are unavailable right now. The news feeds did not respond — this
          panel fills in automatically once they do.
        </p>
      ) : (
        <ul
          className={`grid gap-x-4 gap-y-1 p-3 ${
            columns === 2 ? "sm:grid-cols-2" : "grid-cols-1"
          }`}
        >
          {items.map((item) => (
            <li key={item.link}>
              <a
                href={item.link}
                target="_blank"
                rel="noopener noreferrer nofollow"
                className="group flex gap-3 rounded-xl p-2.5 transition hover:bg-panel-2"
              >
                <NewsThumb
                  src={item.image}
                  alt=""
                  source={item.source}
                  className="h-[68px] w-[104px] shrink-0"
                />
                <div className="min-w-0 flex-1">
                  <p className="text-[10px] font-semibold uppercase tracking-wide text-[#7c8698]">
                    {timeAgo(item.publishedAt)}
                    {item.publishedAt ? " · " : ""}
                    {item.source}
                  </p>
                  <p className="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-white group-hover:text-brand">
                    {item.title}
                  </p>
                  {item.category && (
                    <p className="mt-1.5 inline-block rounded border border-line px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#9aa3b2]">
                      {item.category}
                    </p>
                  )}
                </div>
              </a>
            </li>
          ))}
        </ul>
      )}

      <p className="border-t border-line px-6 py-3 text-[11px] text-[#7c8698]">
        Headlines, images and links belong to the publishers. Actuatrades does not
        endorse them and they are not investment advice.
      </p>
    </div>
  );
}
