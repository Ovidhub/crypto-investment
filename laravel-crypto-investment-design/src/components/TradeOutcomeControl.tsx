"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Policy = "manual" | "win" | "loss" | "market";

const OPTIONS: { key: Policy; label: string; blurb: string }[] = [
  {
    key: "win",
    label: "Always win",
    blurb: "Every trade pays out: stake back plus 85% profit.",
  },
  {
    key: "loss",
    label: "Always lose",
    blurb: "Every trade forfeits its stake to the house.",
  },
  {
    key: "market",
    label: "Follow market",
    blurb: "Judged on live price vs entry; a tie voids.",
  },
  {
    key: "manual",
    label: "Manual",
    blurb: "Nothing settles until you decide it.",
  },
];

export default function TradeOutcomeControl({
  userId,
  name,
  policy,
  openCount,
  openStake,
}: {
  userId: number;
  name: string;
  policy: Policy;
  openCount: number;
  openStake: string;
}) {
  const router = useRouter();
  const [selected, setSelected] = useState<Policy>(policy);
  const [reason, setReason] = useState("");
  const [applyNow, setApplyNow] = useState(openCount > 0);
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const changed = selected !== policy;
  const willSettle = applyNow && selected !== "manual" && openCount > 0;

  async function save() {
    const chosen = OPTIONS.find((o) => o.key === selected)!;
    const consequence = willSettle
      ? `\n\nThis will also settle ${openCount} open trade${openCount === 1 ? "" : "s"} ($${openStake} in stakes) as: ${chosen.label.toLowerCase()}.`
      : "";
    if (
      !window.confirm(
        `Set ${name}'s trade outcome policy to "${chosen.label}"?\n\n${chosen.blurb}${consequence}\n\nEvery settlement posts a balanced ledger journal and is recorded in the audit trail.`,
      )
    ) {
      return;
    }

    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/admin/trade-policy", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ userId, policy: selected, reason, applyNow: willSettle }),
      });
      const data = (await res.json()) as {
        error?: string;
        message?: string;
        settledNow?: number;
        failures?: string[];
      };
      if (!res.ok) {
        setMessage({ tone: "error", text: data.error ?? "Could not save the policy." });
      } else {
        const warning = data.failures?.length
          ? ` Some trades could not settle: ${data.failures.join("; ")}`
          : "";
        setMessage({ tone: "ok", text: `${data.message ?? "Saved."}${warning}` });
        setReason("");
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error. Try again." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="space-y-4 rounded-2xl border border-line bg-card p-6">
      <div>
        <h2 className="font-display font-bold">Trade outcome control</h2>
        <p className="mt-1 text-xs leading-relaxed text-[#9aa3b2]">
          Decide how this account&apos;s trades resolve. A standing policy
          settles each trade when it reaches its expiry — no click per trade — and the
          customer&apos;s receipt shows it was a house decision, never a market event
          that did not happen.
        </p>
      </div>

      <div className="grid gap-2 sm:grid-cols-2">
        {OPTIONS.map((option) => {
          const active = selected === option.key;
          const saved = policy === option.key;
          return (
            <button
              key={option.key}
              type="button"
              onClick={() => setSelected(option.key)}
              className={`rounded-xl border px-4 py-3 text-left transition-colors ${
                active
                  ? option.key === "loss"
                    ? "border-[#f6465d]/60 bg-[#f6465d]/10"
                    : option.key === "win"
                      ? "border-accent/60 bg-accent/10"
                      : "border-brand/60 bg-brand/10"
                  : "border-line bg-panel-2 hover:border-brand/40"
              }`}
            >
              <span className="flex items-center gap-2 text-sm font-semibold text-white">
                {option.label}
                {saved && (
                  <span className="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold text-[#c7cedb]">
                    CURRENT
                  </span>
                )}
              </span>
              <span className="mt-0.5 block text-[11px] leading-snug text-[#9aa3b2]">
                {option.blurb}
              </span>
            </button>
          );
        })}
      </div>

      <label className="block">
        <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
          Internal note (optional)
        </span>
        <textarea
          value={reason}
          onChange={(e) => setReason(e.target.value)}
          rows={2}
          maxLength={300}
          placeholder="e.g. Support case #482 — goodwill credit approved by risk."
          className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
        />
        <span className="mt-1 block text-[11px] text-[#7c8698]">
          Stored in the audit trail and in the customer&apos;s notification. {reason.length}/300
        </span>
      </label>

      {openCount > 0 && selected !== "manual" && (
        <label className="flex items-start gap-2.5 rounded-xl border border-line bg-panel-2 px-4 py-3 text-xs text-[#c7cedb]">
          <input
            type="checkbox"
            checked={applyNow}
            onChange={(e) => setApplyNow(e.target.checked)}
            className="mt-0.5 h-3.5 w-3.5 accent-[#14b8a6]"
          />
          <span>
            Also settle the {openCount} open trade{openCount === 1 ? "" : "s"} now
            (${openStake} in stakes). Leave unticked to let the policy start with the
            next trade.
          </span>
        </label>
      )}

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
        type="button"
        onClick={save}
        disabled={busy || (!changed && !willSettle)}
        className="w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {busy
          ? "Saving…"
          : changed
            ? `Set policy to ${OPTIONS.find((o) => o.key === selected)?.label}`
            : willSettle
              ? `Settle ${openCount} open trade${openCount === 1 ? "" : "s"}`
              : "Policy saved"}
      </button>

      <p className="text-[11px] leading-relaxed text-[#7c8698]">
        Wins post a house payout expense, losses post house settlement revenue, and
        voids return the stake. Nothing here edits a balance directly, and every
        settlement lands in the audit trail with the amount and the note.
      </p>
    </div>
  );
}
