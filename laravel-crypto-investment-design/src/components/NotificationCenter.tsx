"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Note = {
  id: number;
  title: string;
  body: string;
  readAt: string | null;
  createdAt: string;
};

export default function NotificationCenter({ items }: { items: Note[] }) {
  const router = useRouter();
  const [busy, setBusy] = useState(false);

  async function markAll() {
    setBusy(true);
    await fetch("/api/notifications", {
      method: "POST",
      headers: { "content-type": "application/json" },
      body: JSON.stringify({}),
    });
    router.refresh();
    setBusy(false);
  }

  return (
    <div className="rounded-2xl border border-line bg-card">
      <div className="flex items-center justify-between border-b border-line px-6 py-4">
        <div>
          <h2 className="font-display font-bold">Notifications</h2>
          <p className="text-xs text-[#7c8698]">
            {items.filter((i) => !i.readAt).length} unread
          </p>
        </div>
        <button
          onClick={markAll}
          disabled={busy}
          className="rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-[#c7cedb] hover:border-brand hover:text-white disabled:opacity-60"
        >
          Mark all read
        </button>
      </div>
      {items.length === 0 ? (
        <p className="p-6 text-sm text-[#9aa3b2]">Nothing here yet.</p>
      ) : (
        <div className="divide-y divide-line/60">
          {items.map((n) => (
            <div key={n.id} className={`px-6 py-4 ${n.readAt ? "opacity-60" : ""}`}>
              <div className="flex items-start justify-between gap-3">
                <p className="text-sm font-semibold text-white">{n.title}</p>
                <span className="shrink-0 text-[11px] text-[#7c8698]">
                  {new Date(n.createdAt).toLocaleDateString("en-US")}
                </span>
              </div>
              <p className="mt-1 text-sm text-[#9aa3b2]">{n.body}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
