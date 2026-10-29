"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Row = {
  id: number;
  userId: number;
  fullName: string;
  country: string;
  documentType: string;
  documentLast4: string;
  status: string;
  createdAt: string;
  userEmail: string | null;
};

export default function KycReviewTable({ rows }: { rows: Row[] }) {
  const router = useRouter();
  const [busyId, setBusyId] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function decide(id: number, decision: "approved" | "rejected") {
    let note = "";
    if (decision === "rejected") {
      const reason = window.prompt("Reason for rejection:");
      if (!reason || reason.trim().length < 3) return;
      note = reason.trim();
    }
    setBusyId(id);
    setError(null);
    try {
      const res = await fetch("/api/admin/kyc", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ id, decision, note }),
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
    return <p className="p-6 text-sm text-[#9aa3b2]">No verification submissions.</p>;
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
              <p className="text-sm font-semibold text-white">{r.fullName}</p>
              <p className="text-xs text-[#7c8698]">
                {r.country} · {r.documentType} ending {r.documentLast4} · {r.userEmail}
              </p>
              <p className="text-[11px] text-[#5d6675]">
                {new Date(r.createdAt).toLocaleString("en-US")}
              </p>
            </div>
            <div className="flex gap-2">
              <button
                onClick={() => decide(r.id, "approved")}
                disabled={busyId === r.id}
                className="rounded-lg bg-accent/15 px-3 py-1.5 text-xs font-semibold text-accent hover:bg-accent/25 disabled:opacity-50"
              >
                Approve
              </button>
              <button
                onClick={() => decide(r.id, "rejected")}
                disabled={busyId === r.id}
                className="rounded-lg bg-[#f6465d]/15 px-3 py-1.5 text-xs font-semibold text-[#f6465d] hover:bg-[#f6465d]/25 disabled:opacity-50"
              >
                Reject
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
