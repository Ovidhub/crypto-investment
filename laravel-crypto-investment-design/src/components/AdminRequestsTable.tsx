"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Row = {
  id: number;
  userId: number;
  userEmail: string | null;
  userName: string | null;
  amountUsd: string;
  status: string;
  createdAt: string;
  reference?: string | null;
  destination?: string | null;
};

export default function AdminRequestsTable({
  kind,
  rows,
  canApprove,
}: {
  kind: "deposit" | "withdrawal";
  rows: Row[];
  canApprove: boolean;
}) {
  const router = useRouter();
  const [busyId, setBusyId] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function act(id: number, approve: boolean) {
    let note = "";
    if (!approve) {
      const reason = window.prompt("Reason for rejection (shown to the customer):");
      if (!reason || reason.trim().length < 3) return;
      note = reason.trim();
    }
    setBusyId(id);
    setError(null);
    try {
      const res = await fetch("/api/admin/requests", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({
          action: `${approve ? "approve" : "reject"}-${kind}`,
          id,
          note,
        }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setError(data.error ?? "Action failed.");
      else router.refresh();
    } catch {
      setError("Network error.");
    } finally {
      setBusyId(null);
    }
  }

  if (rows.length === 0) {
    return <p className="p-6 text-sm text-[#9aa3b2]">Nothing waiting for review.</p>;
  }

  return (
    <div>
      {error && (
        <p className="mx-6 mt-4 rounded-lg border border-[#f6465d]/30 bg-[#f6465d]/5 px-3 py-2 text-xs text-[#f6465d]">
          {error}
        </p>
      )}
      <div className="divide-y divide-line/60">
        {rows.map((r) => (
          <div key={r.id} className="flex flex-wrap items-center gap-3 px-6 py-4">
            <div className="min-w-0 flex-1">
              <p className="text-sm font-semibold tabular-nums text-white">
                ${Number(r.amountUsd).toLocaleString("en-US", { minimumFractionDigits: 2 })}
              </p>
              <p className="truncate text-xs text-[#7c8698]">
                {r.userName ?? "Unknown"} · {r.userEmail ?? "—"}
              </p>
              <p className="truncate text-[11px] text-[#5d6675]">
                {r.reference ? `${r.reference} · ` : ""}
                {r.destination ? `${r.destination} · ` : ""}
                {new Date(r.createdAt).toLocaleString("en-US")}
              </p>
            </div>
            {canApprove ? (
              <div className="flex gap-2">
                <button
                  onClick={() => act(r.id, true)}
                  disabled={busyId === r.id}
                  className="rounded-lg bg-accent/15 px-3 py-1.5 text-xs font-semibold text-accent hover:bg-accent/25 disabled:opacity-50"
                >
                  {busyId === r.id ? "…" : "Approve"}
                </button>
                <button
                  onClick={() => act(r.id, false)}
                  disabled={busyId === r.id}
                  className="rounded-lg bg-[#f6465d]/15 px-3 py-1.5 text-xs font-semibold text-[#f6465d] hover:bg-[#f6465d]/25 disabled:opacity-50"
                >
                  Reject
                </button>
              </div>
            ) : (
              <span className="text-[11px] text-[#7c8698]">Admin approval required</span>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
