"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type RequestRow = {
  id: number;
  type: "deposit" | "withdrawal";
  amountUsd: string;
  status: string;
  reference?: string | null;
  destination?: string | null;
  createdAt: string;
  adminNote?: string | null;
};

export default function WalletPanel({
  cash,
  locked,
  requests,
}: {
  cash: string;
  locked: string;
  requests: RequestRow[];
}) {
  const router = useRouter();
  const [tab, setTab] = useState<"deposit" | "withdraw">("deposit");
  const [amount, setAmount] = useState("");
  const [destination, setDestination] = useState("");
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(
    null,
  );

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch(`/api/wallet/${tab}`, {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify(
          tab === "deposit" ? { amount } : { amount, destination },
        ),
      });
      const data = (await res.json()) as { error?: string; message?: string };
      if (!res.ok) {
        setMessage({ tone: "error", text: data.error ?? "Request failed." });
      } else {
        setMessage({ tone: "ok", text: data.message ?? "Request submitted." });
        setAmount("");
        setDestination("");
        router.refresh();
      }
    } catch {
      setMessage({ tone: "error", text: "Network error. Try again." });
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="grid gap-6 lg:grid-cols-[1fr_1.1fr]">
      <div className="space-y-4">
        <div className="rounded-2xl border border-line bg-gradient-to-br from-brand/15 to-brand-2/5 p-6">
          <p className="text-xs text-[#9aa3b2]">Available simulated cash</p>
          <p className="mt-2 font-display text-3xl font-bold tabular-nums text-white">
            ${Number(cash).toLocaleString("en-US", { minimumFractionDigits: 2 })}
          </p>
          <p className="mt-2 text-xs text-[#c7cedb]">
            Reserved in pending withdrawals: $
            {Number(locked).toLocaleString("en-US", { minimumFractionDigits: 2 })}
          </p>
        </div>

        <div className="rounded-2xl border border-line bg-card p-6">
          <div className="mb-5 grid grid-cols-2 gap-1 rounded-xl bg-panel-2 p-1">
            {(["deposit", "withdraw"] as const).map((key) => (
              <button
                key={key}
                onClick={() => {
                  setTab(key);
                  setMessage(null);
                }}
                className={`rounded-lg px-4 py-2 text-sm font-semibold capitalize transition-colors ${
                  tab === key
                    ? "bg-gradient-to-r from-brand to-brand-2 text-white"
                    : "text-[#9aa3b2] hover:text-white"
                }`}
              >
                {key === "deposit" ? "Add funds" : "Withdraw"}
              </button>
            ))}
          </div>

          <form onSubmit={submit} className="space-y-4">
            <div>
              <label className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                Amount (USD)
              </label>
              <input
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                inputMode="decimal"
                placeholder="1000.00"
                className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm tabular-nums outline-none focus:border-brand"
              />
              <div className="mt-2 flex gap-2">
                {["100", "500", "1000", "5000"].map((preset) => (
                  <button
                    key={preset}
                    type="button"
                    onClick={() => setAmount(preset)}
                    className="rounded-lg border border-line px-2.5 py-1 text-xs text-[#c7cedb] hover:border-brand hover:text-white"
                  >
                    ${preset}
                  </button>
                ))}
              </div>
            </div>

            {tab === "withdraw" && (
              <div>
                <label className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                  Destination (simulated wallet address)
                </label>
                <input
                  value={destination}
                  onChange={(e) => setDestination(e.target.value)}
                  placeholder="sim-wallet: 0x…"
                  className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand"
                />
              </div>
            )}

            <p className="rounded-xl border border-line bg-panel-2 px-4 py-3 text-xs text-[#9aa3b2]">
              {tab === "deposit"
                ? "This is a simulated deposit request. No payment is taken and no card or bank details are collected. An admin approves it in the queue, then virtual funds appear in your wallet."
                : "Withdrawals are simulated too. Your funds are reserved immediately, then an admin reviews the request. Nothing is sent to a real blockchain address."}
            </p>

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
              {busy ? "Submitting…" : tab === "deposit" ? "Request deposit" : "Request withdrawal"}
            </button>
          </form>
        </div>
      </div>

      <div className="rounded-2xl border border-line bg-card">
        <div className="border-b border-line px-6 py-4">
          <h2 className="font-display font-bold">Your requests</h2>
          <p className="text-xs text-[#7c8698]">Every request is reviewed by an operator.</p>
        </div>
        {requests.length === 0 ? (
          <p className="p-6 text-sm text-[#9aa3b2]">No requests yet.</p>
        ) : (
          <div className="divide-y divide-line/60">
            {requests.map((r) => (
              <div key={`${r.type}-${r.id}`} className="px-6 py-4">
                <div className="flex items-center justify-between gap-3">
                  <div>
                    <p className="text-sm font-semibold capitalize text-white">
                      {r.type} · ${Number(r.amountUsd).toLocaleString("en-US", { minimumFractionDigits: 2 })}
                    </p>
                    <p className="text-xs text-[#7c8698]">
                      {new Date(r.createdAt).toLocaleString("en-US")}
                      {r.reference ? ` · ${r.reference}` : ""}
                    </p>
                  </div>
                  <StatusTag status={r.status} />
                </div>
                {r.destination && (
                  <p className="mt-1 truncate text-xs text-[#7c8698]">{r.destination}</p>
                )}
                {r.adminNote && (
                  <p className="mt-2 rounded-lg bg-panel-2 px-3 py-2 text-xs text-[#c7cedb]">
                    Admin: {r.adminNote}
                  </p>
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

function StatusTag({ status }: { status: string }) {
  const tone =
    status === "approved"
      ? "bg-accent/15 text-accent"
      : status === "pending"
        ? "bg-[#f5a524]/15 text-[#f5a524]"
        : "bg-[#f6465d]/15 text-[#f6465d]";
  return (
    <span className={`rounded-full px-2.5 py-0.5 text-[11px] font-semibold capitalize ${tone}`}>
      {status}
    </span>
  );
}
