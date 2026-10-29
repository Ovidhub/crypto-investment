"use client";

import { useRef, useState } from "react";

export type MediaRow = {
  id: number;
  filename: string;
  mime: string;
  sizeBytes: number;
  url: string;
  createdAt: string;
};

function kb(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

export default function MediaManager({ initial }: { initial: MediaRow[] }) {
  const [items, setItems] = useState<MediaRow[]>(initial);
  const [busy, setBusy] = useState(false);
  const [notice, setNotice] = useState<{ tone: "ok" | "error"; text: string } | null>(
    null,
  );
  const [copied, setCopied] = useState<number | null>(null);
  const input = useRef<HTMLInputElement | null>(null);

  async function upload(file: File) {
    setBusy(true);
    setNotice(null);
    try {
      const form = new FormData();
      form.append("file", file);
      const res = await fetch("/api/admin/media", { method: "POST", body: form });
      const data = (await res.json()) as { error?: string; item?: MediaRow };
      if (!res.ok || !data.item) {
        setNotice({ tone: "error", text: data.error ?? "Upload failed." });
        return;
      }
      setItems((prev) => [data.item as MediaRow, ...prev]);
      setNotice({ tone: "ok", text: `${data.item.filename} uploaded.` });
    } catch {
      setNotice({ tone: "error", text: "Upload failed. Try again." });
    } finally {
      setBusy(false);
    }
  }

  async function remove(id: number) {
    if (!confirm("Delete this image? Any page still pointing at it will lose the image.")) {
      return;
    }
    setBusy(true);
    try {
      const res = await fetch(`/api/admin/media/${id}`, { method: "DELETE" });
      if (!res.ok) {
        const data = (await res.json()) as { error?: string };
        setNotice({ tone: "error", text: data.error ?? "Could not delete." });
        return;
      }
      setItems((prev) => prev.filter((item) => item.id !== id));
      setNotice({ tone: "ok", text: "Image deleted." });
    } finally {
      setBusy(false);
    }
  }

  async function copy(item: MediaRow) {
    try {
      await navigator.clipboard.writeText(item.url);
      setCopied(item.id);
      setTimeout(() => setCopied(null), 1500);
    } catch {
      setNotice({ tone: "error", text: "Copy failed — select the URL manually." });
    }
  }

  return (
    <div className="space-y-6">
      {notice && (
        <p
          className={`rounded-xl border px-4 py-2.5 text-sm ${
            notice.tone === "ok"
              ? "border-accent/40 bg-accent/10 text-accent"
              : "border-danger/40 bg-danger/10 text-danger"
          }`}
        >
          {notice.text}
        </p>
      )}

      <div className="rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display text-lg font-bold text-white">Upload an image</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          PNG, JPEG, WEBP, GIF or AVIF up to 2 MB. Images are stored in the database
          and served from this site, so they survive deploys.
        </p>
        <input
          ref={input}
          type="file"
          accept="image/png,image/jpeg,image/webp,image/gif,image/avif"
          className="hidden"
          onChange={(e) => {
            const file = e.target.files?.[0];
            if (file) void upload(file);
            e.target.value = "";
          }}
        />
        <button
          type="button"
          onClick={() => input.current?.click()}
          disabled={busy}
          className="mt-4 rounded-full bg-gradient-to-r from-brand to-brand-2 px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
        >
          {busy ? "Working…" : "Choose a file"}
        </button>
      </div>

      <div className="rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display text-lg font-bold text-white">
          Library{" "}
          <span className="text-sm font-normal text-[#7c8698]">({items.length})</span>
        </h2>
        {items.length === 0 ? (
          <p className="mt-3 text-sm text-[#9aa3b2]">
            Nothing uploaded yet. The site falls back to its built-in artwork.
          </p>
        ) : (
          <div className="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {items.map((item) => (
              <div key={item.id} className="rounded-xl border border-line bg-panel-2 p-3">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={item.url}
                  alt={item.filename}
                  className="h-32 w-full rounded-lg object-cover"
                />
                <p className="mt-2 truncate text-xs font-semibold text-white">
                  {item.filename}
                </p>
                <p className="text-[11px] text-[#7c8698]">
                  {kb(item.sizeBytes)} · {item.mime.replace("image/", "")}
                </p>
                <div className="mt-2 flex gap-2">
                  <button
                    type="button"
                    onClick={() => copy(item)}
                    className="flex-1 rounded-lg border border-line px-2 py-1.5 text-[11px] font-semibold text-[#c7cedb] hover:bg-white/5"
                  >
                    {copied === item.id ? "Copied" : "Copy URL"}
                  </button>
                  <button
                    type="button"
                    onClick={() => remove(item.id)}
                    className="rounded-lg border border-danger/40 px-2 py-1.5 text-[11px] font-semibold text-danger hover:bg-danger/10"
                  >
                    Delete
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
