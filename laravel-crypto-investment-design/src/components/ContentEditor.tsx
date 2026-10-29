"use client";

import { useMemo, useRef, useState } from "react";

export type EditorField = {
  key: string;
  label: string;
  type: "text" | "textarea" | "image";
  value: string;
  help?: string;
  maxLength?: number;
};

export type EditorGroup = {
  id: string;
  title: string;
  description: string;
  fields: EditorField[];
};

type MediaOption = { id: number; url: string; filename: string };

export default function ContentEditor({
  groups,
  media,
}: {
  groups: EditorGroup[];
  media: MediaOption[];
}) {
  const [activeId, setActiveId] = useState(groups[0]?.id ?? "");
  const [values, setValues] = useState<Record<string, string>>(() => {
    const map: Record<string, string> = {};
    for (const group of groups) {
      for (const field of group.fields) map[field.key] = field.value;
    }
    return map;
  });
  const [busy, setBusy] = useState(false);
  const [uploading, setUploading] = useState<string | null>(null);
  const [notice, setNotice] = useState<{ tone: "ok" | "error"; text: string } | null>(
    null,
  );
  const [library, setLibrary] = useState<MediaOption[]>(media);
  const fileInputs = useRef<Record<string, HTMLInputElement | null>>({});

  const active = useMemo(
    () => groups.find((g) => g.id === activeId) ?? groups[0],
    [groups, activeId],
  );

  function set(key: string, value: string) {
    setValues((prev) => ({ ...prev, [key]: value }));
  }

  async function upload(key: string, file: File) {
    setUploading(key);
    setNotice(null);
    try {
      const form = new FormData();
      form.append("file", file);
      const res = await fetch("/api/admin/media", { method: "POST", body: form });
      const data = (await res.json()) as {
        error?: string;
        item?: MediaOption & { sizeBytes: number };
      };
      if (!res.ok || !data.item) {
        setNotice({ tone: "error", text: data.error ?? "Upload failed." });
        return;
      }
      const item = { id: data.item.id, url: data.item.url, filename: data.item.filename };
      setLibrary((prev) => [item, ...prev]);
      set(key, item.url);
      setNotice({ tone: "ok", text: "Image uploaded and selected. Save to publish." });
    } catch {
      setNotice({ tone: "error", text: "Upload failed. Try a smaller image." });
    } finally {
      setUploading(null);
    }
  }

  async function save() {
    setBusy(true);
    setNotice(null);
    try {
      const res = await fetch("/api/admin/content", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ values }),
      });
      const data = (await res.json()) as { error?: string; message?: string };
      if (!res.ok) {
        setNotice({ tone: "error", text: data.error ?? "Could not save." });
        return;
      }
      setNotice({ tone: "ok", text: data.message ?? "Saved." });
    } catch {
      setNotice({ tone: "error", text: "Network error. Please try again." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="grid gap-6 lg:grid-cols-[240px_1fr]">
      <nav className="space-y-1">
        {groups.map((group) => (
          <button
            key={group.id}
            type="button"
            onClick={() => setActiveId(group.id)}
            className={`w-full rounded-xl px-3 py-2 text-left text-sm transition-colors ${
              group.id === active?.id
                ? "bg-brand/15 font-semibold text-white"
                : "text-[#9aa3b2] hover:bg-white/5 hover:text-white"
            }`}
          >
            {group.title}
          </button>
        ))}
        <a
          href="/"
          target="_blank"
          rel="noreferrer"
          className="mt-3 block rounded-xl border border-line px-3 py-2 text-center text-xs font-semibold text-[#c7cedb] hover:bg-white/5"
        >
          Preview site ↗
        </a>
      </nav>

      <div className="space-y-5">
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
          <h2 className="font-display text-lg font-bold text-white">{active?.title}</h2>
          <p className="mt-1 text-xs text-[#9aa3b2]">{active?.description}</p>

          <div className="mt-5 space-y-5">
            {active?.fields.map((field) => (
              <label key={field.key} className="block">
                <span className="text-xs font-semibold uppercase tracking-wide text-[#7c8698]">
                  {field.label}
                </span>

                {field.type === "textarea" ? (
                  <textarea
                    value={values[field.key] ?? ""}
                    onChange={(e) => set(field.key, e.target.value)}
                    maxLength={field.maxLength ?? 2000}
                    rows={3}
                    className="mt-1.5 w-full rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
                  />
                ) : field.type === "image" ? (
                  <div className="mt-1.5 space-y-2">
                    <div className="flex items-center gap-3">
                      {values[field.key] ? (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img
                          src={values[field.key]}
                          alt=""
                          className="h-14 w-14 rounded-lg border border-line object-cover"
                        />
                      ) : (
                        <div className="grid h-14 w-14 place-items-center rounded-lg border border-dashed border-line text-[10px] text-[#7c8698]">
                          none
                        </div>
                      )}
                      <input
                        value={values[field.key] ?? ""}
                        onChange={(e) => set(field.key, e.target.value)}
                        placeholder="/images/hero.png or https://…"
                        className="w-full rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
                      />
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                      <input
                        ref={(el) => {
                          fileInputs.current[field.key] = el;
                        }}
                        type="file"
                        accept="image/png,image/jpeg,image/webp,image/gif,image/avif"
                        className="hidden"
                        onChange={(e) => {
                          const file = e.target.files?.[0];
                          if (file) void upload(field.key, file);
                          e.target.value = "";
                        }}
                      />
                      <button
                        type="button"
                        onClick={() => fileInputs.current[field.key]?.click()}
                        disabled={uploading === field.key}
                        className="rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-[#c7cedb] hover:bg-white/5 disabled:opacity-50"
                      >
                        {uploading === field.key ? "Uploading…" : "Upload image"}
                      </button>
                      {library.length > 0 && (
                        <select
                          value=""
                          onChange={(e) => {
                            if (e.target.value) set(field.key, e.target.value);
                          }}
                          className="rounded-lg border border-line bg-panel-2 px-2 py-1.5 text-xs text-[#c7cedb]"
                        >
                          <option value="">Choose from library…</option>
                          {library.map((item) => (
                            <option key={item.id} value={item.url}>
                              {item.filename}
                            </option>
                          ))}
                        </select>
                      )}
                      {values[field.key] && (
                        <button
                          type="button"
                          onClick={() => set(field.key, "")}
                          className="rounded-lg border border-line px-3 py-1.5 text-xs text-[#9aa3b2] hover:bg-white/5"
                        >
                          Clear
                        </button>
                      )}
                    </div>
                  </div>
                ) : (
                  <input
                    value={values[field.key] ?? ""}
                    onChange={(e) => set(field.key, e.target.value)}
                    maxLength={field.maxLength ?? 300}
                    className="mt-1.5 w-full rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
                  />
                )}

                {field.help && (
                  <span className="mt-1 block text-[11px] text-[#7c8698]">{field.help}</span>
                )}
              </label>
            ))}
          </div>
        </div>

        <div className="sticky bottom-4 flex items-center justify-between rounded-2xl border border-line bg-panel px-5 py-3">
          <p className="text-xs text-[#7c8698]">
            Blank fields fall back to the built-in copy. Changes go live on the next
            page load.
          </p>
          <button
            type="button"
            onClick={save}
            disabled={busy}
            className="rounded-full bg-gradient-to-r from-brand to-brand-2 px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
          >
            {busy ? "Saving…" : "Save changes"}
          </button>
        </div>
      </div>
    </div>
  );
}
