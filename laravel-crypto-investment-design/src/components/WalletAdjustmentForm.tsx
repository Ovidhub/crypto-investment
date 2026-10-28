"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type UserOption = { id: number; name: string; email: string; cash: string };

export default function WalletAdjustmentForm({
  users,
  fixedUser,
  maxAmount = 50000,
}: {
  users: UserOption[];
  fixedUser?: { id: number; name: string; cash: string; email?: string };
  maxAmount?: number;
}) {
  const router = useRouter();
  const [userId, setUserId] = useState<string>(
    fixedUser ? String(fixedUser.id) : users[0] ? String(users[0].id) : "",
  );
  const [direction, setDirection] = useState<"credit" | "debit">("credit");
  const [amount, setAmount] = useState("");
  const [reason, setReason] = useState("");
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const selected = fixedUser ?? users.find((u) => String(u.id) === userId);
  const parsed = Number(amount);
  const valid =
    parsed > 0 &&
    parsed <= maxAmount &&
    reason.trim().length >= 8 &&
    Number.isFinite(parsed) &&
    (direction === "credit" || (selected ? parsed <= Number(selected.cash) : false));

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    const verb = direction === "credit" ? "credit" : "debit";
    const who = selected
      ? `${selected.name}${selected.email ? ` (${selected.email})` : ""}`
      : `user #${userId}`;
    if (
      !window.confirm(
        `Confirm: ${verb} $${parsed.toFixed(2)} ${direction === "credit" ? "to" : "from"} ${who}.\n\nThis posts a balanced ledger journal and is written to the audit trail.`,
      )
    ) {
      return;
    }
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/admin/adjustments", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ userId: Number(userId), direction, amount, reason }),
      });
      const data = (await res.json()) as { error?: string; message?: string; cashAfter?: string };
      if (!res.ok) {
        setMessage({ tone: "error", text: data.error ?? "Adjustment failed." });
      } else {
        setMessage({
          tone: "ok",
          text: `${data.message ?? "Done."} New balance $${data.cashAfter}.`,
        });
        setAmount("");
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
    <form onSubmit={submit} className="space-y-4 rounded-2xl border border-line bg-card p-6">
      <div>
        <h2 className="font-display font-bold">
          {fixedUser ? `Adjust ${fixedUser.name}'s wallet` : "Fund or debit a wallet"}
        </h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          Posts a balanced journal against the operator-adjustment equity
          account. It never touches revenue or yield, so it cannot look like
          trading performance.
        </p>
      </div>

      {!fixedUser && (
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Account</span>
          <select
            value={userId}
            onChange={(e) => setUserId(e.target.value)}
            className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
          >
            {users.map((u) => (
              <option key={u.id} value={u.id}>
                {u.name} — {u.email} (${Number(u.cash).toFixed(2)})
              </option>
            ))}
          </select>
        </label>
      )}

      <div className="grid grid-cols-2 gap-1 rounded-xl bg-panel-2 p-1">
        {(["credit", "debit"] as const).map((d) => (
          <button
            key={d}
            type="button"
            onClick={() => setDirection(d)}
            className={`rounded-lg px-4 py-2 text-sm font-semibold capitalize transition-colors ${
              direction === d
                ? d === "credit"
                  ? "bg-accent text-ink"
                  : "bg-[#f6465d] text-white"
                : "text-[#9aa3b2] hover:text-white"
            }`}
          >
            {d === "credit" ? "Fund (credit)" : "Debit"}
          </button>
        ))}
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Amount (USD)</span>
          <input
            value={amount}
            onChange={(e) => setAmount(e.target.value.replace(/[^0-9.]/g, ""))}
            inputMode="decimal"
            placeholder="250.00"
            className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm tabular-nums outline-none focus:border-brand"
          />
        </label>
        <div className="rounded-xl border border-line bg-panel-2 px-4 py-2.5 text-xs text-[#9aa3b2]">
          <p>Available now</p>
          <p className="mt-1 font-display text-lg font-bold tabular-nums text-white">
            ${selected ? Number(selected.cash).toLocaleString("en-US", { minimumFractionDigits: 2 }) : "—"}
          </p>
          <p className="mt-1 text-[11px]">Cap ${maxAmount.toLocaleString()} per action</p>
        </div>
      </div>

      <label className="block">
        <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
          Reason (shown to the customer, min 8 characters)
        </span>
        <textarea
          value={reason}
          onChange={(e) => setReason(e.target.value)}
          rows={3}
          maxLength={300}
          placeholder="e.g. Goodwill credit for the duplicate fee on trade #481"
          className="w-full rounded-xl border border-line bg-ink px-4 py-2.5 text-sm outline-none focus:border-brand"
        />
        <span className="mt-1 block text-[11px] text-[#7c8698]">{reason.trim().length}/300</span>
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
        disabled={busy || !valid}
        className={`w-full rounded-xl px-5 py-3 text-sm font-semibold disabled:opacity-50 ${
          direction === "credit"
            ? "bg-gradient-to-r from-brand to-brand-2 text-white"
            : "bg-[#f6465d] text-white"
        }`}
      >
        {busy
          ? "Posting…"
          : direction === "credit"
            ? "Credit wallet"
            : "Debit wallet"}
      </button>

      <p className="text-[11px] leading-relaxed text-[#7c8698]">
        Hard limits: no self-adjustment, minimum $1.00, maximum $50,000 per
        action, debit cannot exceed available cash, and every entry stores the
        operator, the amount and the reason in the audit trail.
      </p>
    </form>
  );
}
