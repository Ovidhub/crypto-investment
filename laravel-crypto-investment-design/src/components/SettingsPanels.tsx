"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

export default function SettingsPanels({ email }: { email: string }) {
  const router = useRouter();
  const [currentPassword, setCurrent] = useState("");
  const [newPassword, setNext] = useState("");
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  async function changePassword(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/account/password", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ currentPassword, newPassword }),
      });
      const data = (await res.json()) as { error?: string; message?: string };
      if (!res.ok) setMessage({ tone: "error", text: data.error ?? "Could not update." });
      else {
        setMessage({ tone: "ok", text: data.message ?? "Password updated." });
        setCurrent("");
        setNext("");
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="space-y-6">
      <form onSubmit={changePassword} className="space-y-4 rounded-2xl border border-line bg-card p-6">
        <div>
          <h2 className="font-display font-bold">Password</h2>
          <p className="mt-1 text-xs text-[#9aa3b2]">Signed in as {email}</p>
        </div>
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Current password</span>
          <input
            type="password"
            value={currentPassword}
            onChange={(e) => setCurrent(e.target.value)}
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand"
          />
        </label>
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
            New password (min 10 characters)
          </span>
          <input
            type="password"
            value={newPassword}
            onChange={(e) => setNext(e.target.value)}
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand"
          />
        </label>
        {message && (
          <p
            className={`rounded-xl border px-4 py-2.5 text-sm ${
              message.tone === "ok"
                ? "border-accent/30 bg-accent/5 text-accent"
                : "border-[#f6465d]/30 bg-[#f6465d]/5 text-[#f6465d]"
            }`}
          >
            {message.text}
          </p>
        )}
        <button
          disabled={busy}
          className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
        >
          {busy ? "Updating…" : "Update password"}
        </button>
      </form>

      <div className="rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display font-bold">Your data</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          Download a JSON copy of your profile, balances and plan positions. The
          export is generated from the ledger, so it always matches what you see.
        </p>
        <a
          href="/api/account/export"
          className="mt-4 inline-block rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
        >
          Download my data
        </a>
      </div>
    </div>
  );
}
