"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { formatUsd } from "@/lib/format";

export default function WalletPanel({ balance }: { balance: number }) {
  const router = useRouter();
  const [tab, setTab] = useState<"deposit" | "withdraw">("deposit");
  const [amount, setAmount] = useState("500");
  const [loading, setLoading] = useState(false);
  const [msg, setMsg] = useState<{ type: "ok" | "err"; text: string } | null>(
    null,
  );

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMsg(null);
    try {
      const res = await fetch("/api/deposit", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: tab, usdAmount: Number(amount) }),
      });
      const data = await res.json();
      if (!res.ok) {
        setMsg({ type: "err", text: data.error ?? "Failed." });
        setLoading(false);
        return;
      }
      setMsg({
        type: "ok",
        text: `${tab === "deposit" ? "Deposited" : "Withdrew"} ${formatUsd(Number(amount))}.`,
      });
      setLoading(false);
      router.refresh();
    } catch {
      setMsg({ type: "err", text: "Network error." });
      setLoading(false);
    }
  }

  return (
    <div className="grid gap-6 lg:grid-cols-2">
      <div className="rounded-2xl border border-line bg-gradient-to-br from-brand/15 to-brand-2/5 p-6">
        <p className="text-sm text-[#9aa3b2]">Available cash balance</p>
        <p className="mt-2 font-display text-4xl font-bold tabular-nums text-white">
          {formatUsd(balance)}
        </p>
        <div className="mt-6 rounded-xl border border-line bg-panel-2/60 p-4">
          <p className="text-xs text-[#9aa3b2]">Your deposit address (demo)</p>
          <p className="mt-1 break-all font-mono text-sm text-brand">
            0xE9a7b31C4f8D2a5e6B0c1F3a9D8e7C4b2A1f6E0d
          </p>
        </div>
        <p className="mt-4 text-xs text-[#7c8698]">
          This is a demo wallet. Deposits and withdrawals adjust your simulated
          cash balance instantly.
        </p>
      </div>

      <div className="rounded-2xl border border-line bg-card p-6">
        <div className="mb-5 grid grid-cols-2 gap-2 rounded-xl border border-line bg-panel-2 p-1">
          {(["deposit", "withdraw"] as const).map((t) => (
            <button
              key={t}
              onClick={() => {
                setTab(t);
                setMsg(null);
              }}
              className={`rounded-lg py-2 text-sm font-semibold capitalize transition-colors ${
                tab === t ? "bg-gradient-to-r from-brand to-brand-2 text-white" : "text-[#9aa3b2]"
              }`}
            >
              {t}
            </button>
          ))}
        </div>

        <form onSubmit={submit} className="space-y-4">
          <div>
            <label className="mb-1.5 block text-xs text-[#9aa3b2]">
              Amount (USD)
            </label>
            <input
              value={amount}
              onChange={(e) => setAmount(e.target.value.replace(/[^0-9.]/g, ""))}
              inputMode="decimal"
              className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none focus:border-brand"
            />
            <div className="mt-2 flex gap-2">
              {[100, 500, 1000, 5000].map((v) => (
                <button
                  key={v}
                  type="button"
                  onClick={() => setAmount(String(v))}
                  className="flex-1 rounded-lg border border-line bg-panel-2 py-1.5 text-xs text-[#c7cedb] hover:border-brand"
                >
                  ${v}
                </button>
              ))}
            </div>
          </div>

          {msg && (
            <p
              className={`text-sm ${msg.type === "ok" ? "text-accent" : "text-[#f6465d]"}`}
            >
              {msg.text}
            </p>
          )}

          <button
            type="submit"
            disabled={loading}
            className="w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.01] disabled:opacity-60"
          >
            {loading ? "Processing…" : `Confirm ${tab}`}
          </button>
        </form>
      </div>
    </div>
  );
}
