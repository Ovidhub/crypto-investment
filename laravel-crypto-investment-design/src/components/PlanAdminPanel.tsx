"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Plan = {
  id: number;
  slug: string;
  name: string;
  tagline: string;
  minAmountUsd: string;
  maxAmountUsd: string;
  annualRateBp: number;
  durationDays: number;
  status: string;
};

export default function PlanAdminPanel({ plans }: { plans: Plan[] }) {
  const router = useRouter();
  const [form, setForm] = useState({
    slug: "",
    name: "",
    tagline: "",
    minAmount: "100.00",
    maxAmount: "10000.00",
    annualRate: "6",
    durationDays: "90",
  });
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  async function save(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/admin/plans", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({
          action: "upsert",
          slug: form.slug,
          name: form.name,
          tagline: form.tagline,
          minAmount: form.minAmount,
          maxAmount: form.maxAmount,
          annualRateBp: Math.round(Number(form.annualRate) * 100),
          durationDays: Number(form.durationDays),
        }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setMessage({ tone: "error", text: data.error ?? "Could not save." });
      else {
        setMessage({ tone: "ok", text: "Plan saved." });
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error." });
    } finally {
      setBusy(false);
    }
  }

  async function toggle(slug: string) {
    await fetch("/api/admin/plans", {
      method: "POST",
      headers: { "content-type": "application/json" },
      body: JSON.stringify({ action: "toggle", slug }),
    });
    router.refresh();
  }

  return (
    <div className="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
      <div className="rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Catalogue</h2>
          <p className="text-xs text-[#7c8698]">
            Rates are simulated annual figures. Changing one does not rewrite
            already-running positions.
          </p>
        </div>
        <div className="divide-y divide-line/60">
          {plans.map((p) => (
            <div key={p.id} className="flex items-center gap-3 px-6 py-4">
              <div className="min-w-0 flex-1">
                <p className="text-sm font-semibold text-white">{p.name}</p>
                <p className="text-xs text-[#7c8698]">
                  {(p.annualRateBp / 100).toFixed(2)}% / yr · {p.durationDays}d · $
                  {Number(p.minAmountUsd).toLocaleString()}–$
                  {Number(p.maxAmountUsd).toLocaleString()}
                </p>
              </div>
              <span
                className={`rounded-full px-2.5 py-0.5 text-[11px] font-semibold capitalize ${
                  p.status === "active"
                    ? "bg-accent/15 text-accent"
                    : "bg-[#f5a524]/15 text-[#f5a524]"
                }`}
              >
                {p.status}
              </span>
              <button
                onClick={() => toggle(p.slug)}
                className="rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
              >
                {p.status === "active" ? "Pause" : "Activate"}
              </button>
            </div>
          ))}
        </div>
      </div>

      <form onSubmit={save} className="space-y-4 rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display font-bold">Add or update a plan</h2>
        {(
          [
            ["slug", "Slug (lowercase, dashes)"],
            ["name", "Display name"],
            ["tagline", "Tagline"],
            ["minAmount", "Minimum (USD)"],
            ["maxAmount", "Maximum (USD)"],
            ["annualRate", "Simulated annual rate (%)"],
            ["durationDays", "Duration (days)"],
          ] as const
        ).map(([key, label]) => (
          <label key={key} className="block">
            <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">{label}</span>
            <input
              value={form[key]}
              onChange={(e) => setForm({ ...form, [key]: e.target.value })}
              className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
            />
          </label>
        ))}
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
          className="w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
        >
          {busy ? "Saving…" : "Save plan"}
        </button>
        <p className="text-[11px] text-[#7c8698]">
          There is intentionally no way to credit a balance from this screen.
          Funds enter a wallet only through an approved deposit, plan accrual or
          trade settlement.
        </p>
      </form>
    </div>
  );
}
