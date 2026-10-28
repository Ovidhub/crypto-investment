"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { formatUsd } from "@/lib/format";

type Row = {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  balanceUsd: string;
  kycStatus: string;
  referralCode: string;
  createdAt: string | Date;
};

const ROLE_OPTIONS = ["user", "support", "admin"];

export default function AdminUsersTable({
  rows,
  currentId,
}: {
  rows: Row[];
  currentId: number;
}) {
  const router = useRouter();
  const [busy, setBusy] = useState<number | null>(null);
  const [query, setQuery] = useState("");
  const [error, setError] = useState<string | null>(null);

  const filtered = rows.filter(
    (r) =>
      r.name.toLowerCase().includes(query.toLowerCase()) ||
      r.email.toLowerCase().includes(query.toLowerCase()) ||
      r.referralCode.toLowerCase().includes(query.toLowerCase()),
  );

  async function act(userId: number, action: string, role?: string) {
    setBusy(userId);
    setError(null);
    try {
      const res = await fetch("/api/admin/users", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ userId, action, role }),
      });
      const data = (await res.json()) as { error?: string };
      if (!res.ok) setError(data.error ?? "Update failed.");
      else router.refresh();
    } catch {
      setError("Network error.");
    } finally {
      setBusy(null);
    }
  }

  return (
    <div className="overflow-hidden rounded-2xl border border-line bg-card">
      <div className="flex flex-wrap items-center gap-3 border-b border-line p-4">
        <input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search name, email or referral code…"
          className="w-full max-w-sm rounded-xl border border-line bg-panel-2 px-4 py-2.5 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
        />
        <span className="text-xs text-[#7c8698]">{filtered.length} shown</span>
      </div>

      {error && (
        <p className="border-b border-line bg-[#f6465d]/5 px-4 py-2 text-xs text-[#f6465d]">
          {error}
        </p>
      )}

      <div className="overflow-x-auto">
        <table className="w-full min-w-[900px] text-left text-sm">
          <thead>
            <tr className="border-b border-line text-xs uppercase tracking-wider text-[#7c8698]">
              <th className="px-6 py-4 font-medium">User</th>
              <th className="px-6 py-4 font-medium">Role</th>
              <th className="px-6 py-4 font-medium">Status</th>
              <th className="px-6 py-4 font-medium">KYC</th>
              <th className="px-6 py-4 text-right font-medium">Cash</th>
              <th className="px-6 py-4 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((u) => (
              <tr key={u.id} className="border-b border-line/60 last:border-0">
                <td className="px-6 py-4">
                  <Link
                    href={`/admin/users/${u.id}`}
                    className="font-semibold text-white hover:text-brand"
                  >
                    {u.name}
                  </Link>
                  <p className="text-xs text-[#7c8698]">{u.email}</p>
                  <p className="font-mono text-[10px] text-[#5d6675]">{u.referralCode}</p>
                </td>
                <td className="px-6 py-4">
                  {u.id === currentId ? (
                    <span className="rounded-full bg-brand/15 px-2.5 py-1 text-xs font-semibold text-brand">
                      {u.role} (you)
                    </span>
                  ) : (
                    <select
                      value={u.role}
                      disabled={busy === u.id}
                      onChange={(e) => act(u.id, "setRole", e.target.value)}
                      className="rounded-lg border border-line bg-panel-2 px-2.5 py-1.5 text-xs font-semibold text-[#c7cedb] outline-none focus:border-brand disabled:opacity-50"
                    >
                      {ROLE_OPTIONS.map((r) => (
                        <option key={r} value={r}>
                          {r}
                        </option>
                      ))}
                    </select>
                  )}
                </td>
                <td className="px-6 py-4">
                  <span
                    className={`rounded-full px-2.5 py-1 text-xs font-semibold ${
                      u.status === "active"
                        ? "bg-accent/15 text-accent"
                        : "bg-[#f6465d]/15 text-[#f6465d]"
                    }`}
                  >
                    {u.status}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <span className="rounded-full bg-white/10 px-2.5 py-1 text-xs font-semibold capitalize text-[#c7cedb]">
                    {u.kycStatus}
                  </span>
                </td>
                <td className="px-6 py-4 text-right tabular-nums text-white">
                  {formatUsd(Number(u.balanceUsd))}
                </td>
                <td className="px-6 py-4">
                  {u.id === currentId ? (
                    <span className="block text-right text-xs text-[#7c8698]">You</span>
                  ) : (
                    <div className="flex justify-end">
                      {u.status === "active" ? (
                        <button
                          disabled={busy === u.id}
                          onClick={() => act(u.id, "suspend")}
                          className="rounded-lg border border-line px-3 py-1.5 text-xs font-medium text-[#f6465d] hover:border-[#f6465d] disabled:opacity-50"
                        >
                          Suspend
                        </button>
                      ) : (
                        <button
                          disabled={busy === u.id}
                          onClick={() => act(u.id, "activate")}
                          className="rounded-lg border border-line px-3 py-1.5 text-xs font-medium text-accent hover:border-accent disabled:opacity-50"
                        >
                          Activate
                        </button>
                      )}
                    </div>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
