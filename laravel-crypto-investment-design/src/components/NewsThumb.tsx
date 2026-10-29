"use client";

import { useState } from "react";

/**
 * Article thumbnail with a quiet fallback.
 *
 * Publisher images are hot-linked from their CDNs, so one can 404 or block the
 * request. Rather than leaving a broken-image icon, fall back to a branded tile
 * carrying the publisher's name — no invented artwork.
 */
export default function NewsThumb({
  src,
  alt,
  source,
  className = "",
}: {
  src: string | null;
  alt: string;
  source: string;
  className?: string;
}) {
  const [failed, setFailed] = useState(false);

  if (!src || failed) {
    return (
      <div
        aria-hidden
        className={`flex items-center justify-center rounded-lg border border-line bg-gradient-to-br from-brand/25 via-panel-2 to-brand-2/20 ${className}`}
      >
        <span className="px-1 text-center font-display text-[10px] font-bold uppercase leading-tight tracking-wide text-white/70">
          {source}
        </span>
      </div>
    );
  }

  return (
    // eslint-disable-next-line @next/next/no-img-element -- remote publisher asset, no loader needed
    <img
      src={src}
      alt={alt}
      loading="lazy"
      decoding="async"
      referrerPolicy="no-referrer"
      onError={() => setFailed(true)}
      className={`rounded-lg border border-line/70 bg-panel-2 object-cover ${className}`}
    />
  );
}
