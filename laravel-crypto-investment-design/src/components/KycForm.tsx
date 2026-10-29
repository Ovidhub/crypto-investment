"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

const COUNTRIES = [
  "Nigeria", "Ghana", "Kenya", "South Africa", "United Kingdom", "United States",
  "Canada", "Germany", "France", "Netherlands", "India", "Singapore", "Brazil",
  "United Arab Emirates", "Other",
];

export default function KycForm({ currentStatus }: { currentStatus: string }) {
  const router = useRouter();
  const [fullName, setFullName] = useState("");
  const [country, setCountry] = useState("Nigeria");
  const [documentType, setDocumentType] = useState("passport");
  const [documentNumber, setDocumentNumber] = useState("");
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState<{ tone: "ok" | "error"; text: string } | null>(null);

  const locked = currentStatus === "pending" || currentStatus === "verified";

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true);
    setMessage(null);
    try {
      const res = await fetch("/api/kyc", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ fullName, country, documentType, documentNumber }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setMessage({ tone: "error", text: data.error ?? "Submission failed." });
      else {
        setMessage({ tone: "ok", text: "Submitted for review." });
        setDocumentNumber("");
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
        <h2 className="font-display font-bold">Identity verification</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          We store only your name, country, document type and the last
          four digits. Do not upload or type a real document number — nothing is
          verified against any registry.
        </p>
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Full legal name</span>
          <input
            value={fullName}
            onChange={(e) => setFullName(e.target.value)}
            disabled={locked}
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand disabled:opacity-60"
          />
        </label>
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Country</span>
          <select
            value={country}
            onChange={(e) => setCountry(e.target.value)}
            disabled={locked}
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand disabled:opacity-60"
          >
            {COUNTRIES.map((c) => (
              <option key={c} value={c}>{c}</option>
            ))}
          </select>
        </label>
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Document type</span>
          <select
            value={documentType}
            onChange={(e) => setDocumentType(e.target.value)}
            disabled={locked}
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm outline-none focus:border-brand disabled:opacity-60"
          >
            <option value="passport">Passport</option>
            <option value="drivers-licence">Driver&apos;s licence</option>
            <option value="national-id">National ID</option>
          </select>
        </label>
        <label className="block">
          <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
            Document number (fake data only)
          </span>
          <input
            value={documentNumber}
            onChange={(e) => setDocumentNumber(e.target.value)}
            disabled={locked}
            placeholder="0000 0000 0000"
            className="w-full rounded-xl border border-line bg-ink px-4 py-3 text-sm tabular-nums outline-none focus:border-brand disabled:opacity-60"
          />
        </label>
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
        disabled={busy || locked}
        className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {locked ? "Submission locked" : busy ? "Submitting…" : "Submit for review"}
      </button>
    </form>
  );
}
