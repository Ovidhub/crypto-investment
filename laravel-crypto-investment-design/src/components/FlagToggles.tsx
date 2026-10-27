"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Flag = { key: string; enabled: boolean; updatedAt: string };

export default function FlagToggles({ flags }: { flags: Flag[] }) {
  const router = useRouter();
  const [busy, setBusy] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function toggle(key: string, enabled: boolean) {
    setBusy(key);
    setError(null);
    try {
      const res = await fetch("/api/admin/settings", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ key, enabled }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setError(data.error ?? "Could not update.");
      else router.refresh();
    } catch {
      setError("Network error.");
    } finally {
      setBusy(null);
    }
  }

  return (
    <div className="rounded-2xl border border-line bg-card">
      <div className="border-b border-line px-6 py-4">
        <h2 className="font-display font-bold">Feature flags</h2>
        <p className="text-xs text-[#7c8698]">
          Turning a flag off blocks new requests immediately; existing positions
          are untouched.
        </p>
      </div>
      {error && <p className="px-6 pt-4 text-xs text-[#f6465d]">{error}</p>}
      <div className="divide-y divide-line/60">
        {flags.map((f) => (
          <div key={f.key} className="flex items-center justify-between px-6 py-4">
            <div>
              <p className="text-sm font-semibold capitalize text-white">{f.key}</p>
              <p className="text-[11px] text-[#7c8698]">
                Updated {new Date(f.updatedAt).toLocaleString("en-US")}
              </p>
            </div>
            <button
              onClick={() => toggle(f.key, !f.enabled)}
              disabled={busy === f.key}
              className={`relative h-6 w-11 rounded-full transition-colors disabled:opacity-50 ${
                f.enabled ? "bg-accent" : "bg-[#2b3442]"
              }`}
              aria-pressed={f.enabled}
              aria-label={`Toggle ${f.key}`}
            >
              <span
                className={`absolute top-0.5 h-5 w-5 rounded-full bg-white transition-transform ${
                  f.enabled ? "translate-x-5" : "translate-x-0.5"
                }`}
              />
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}
