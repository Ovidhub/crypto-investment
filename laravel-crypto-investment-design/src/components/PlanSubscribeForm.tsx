"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

export default function PlanSubscribeForm({
  slug,
  name,
  minUsd,
  maxUsd,
  annualRateBp,
  durationDays,
  cash,
}: {
  slug: string;
  name: string;
  minUsd: string;
  maxUsd: string;
  annualRateBp: number;
  durationDays: number;
  cash: number;
}) {
  const router = useRouter();
  const [amount, setAmount] = useState(minUsd);
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const parsed = Number(amount);
  const valid = Number.isFinite(parsed) && parsed >= Number(minUsd) && parsed <= Number(maxUsd);
  const dailyEstimate = valid ? (parsed * (annualRateBp / 10000)) / 365 : 0;
  const totalEstimate = dailyEstimate * durationDays;

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/plans/subscribe", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ slug, amount }),
      });
      const data = (await res.json()) as { error?: string; message?: string };
      if (!res.ok) setMessage({ tone: "error", text: data.error ?? "Could not start the plan." });
      else {
        setMessage({ tone: "ok", text: data.message ?? "Plan started." });
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error. Try again." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <form onSubmit={submit} className="space-y-4 rounded-2xl border border-line bg-card p-6">
      <div>
        <h3 className="font-display font-bold">Fund {name}</h3>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          $ {Number(minUsd).toLocaleString()} – $ {Number(maxUsd).toLocaleString()} ·{" "}
          {durationDays} days
        </p>
      </div>

      <div>
        <label className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
          Amount to lock
        </label>
        <input
          value={amount}
          onChange={(e) => setAmount(e.target.value)}
          inputMode="decimal"
          className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm tabular-nums outline-none focus:border-brand"
        />
        <p className="mt-1.5 text-xs text-[#7c8698]">
          Available cash: ${cash.toLocaleString("en-US", { minimumFractionDigits: 2 })}
        </p>
      </div>

      <div className="rounded-xl border border-line bg-panel-2 p-4 text-xs text-[#9aa3b2]">
        <p className="font-semibold text-[#c7cedb]">Projection</p>
        <div className="mt-2 flex justify-between">
          <span>Per day</span>
          <span className="tabular-nums text-white">${dailyEstimate.toFixed(2)}</span>
        </div>
        <div className="flex justify-between">
          <span>If it ran the full term</span>
          <span className="tabular-nums text-white">${totalEstimate.toFixed(2)}</span>
        </div>
        <p className="mt-2 text-[11px] text-[#7c8698]">
          This is arithmetic on your target rate, not a forecast or a promise.
          Credits are booked daily from plan performance and can be reduced or
          paused at any time.
        </p>
      </div>

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
        disabled={busy || !valid}
        className="w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {busy ? "Starting…" : "Start plan"}
      </button>
    </form>
  );
}
