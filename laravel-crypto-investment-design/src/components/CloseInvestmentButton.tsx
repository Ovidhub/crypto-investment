"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

export default function CloseInvestmentButton({ id }: { id: number }) {
  const router = useRouter();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function close() {
    if (!window.confirm("Release the principal now and stop further simulated accrual?")) {
      return;
    }
    setBusy(true);
    setError(null);
    try {
      const res = await fetch("/api/plans/close", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ id }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setError(data.error ?? "Could not close the simulation.");
      else router.refresh();
    } catch {
      setError("Network error.");
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="text-right">
      <button
        onClick={close}
        disabled={busy}
        className="rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-[#c7cedb] hover:border-[#f6465d] hover:text-[#f6465d] disabled:opacity-60"
      >
        {busy ? "Closing…" : "Close early"}
      </button>
      {error && <p className="mt-1 text-[11px] text-[#f6465d]">{error}</p>}
    </div>
  );
}
