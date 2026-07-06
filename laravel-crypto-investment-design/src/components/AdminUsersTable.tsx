"use client";

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
  createdAt: string | Date;
};

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

  const filtered = rows.filter(
    (r) =>
      r.name.toLowerCase().includes(query.toLowerCase()) ||
      r.email.toLowerCase().includes(query.toLowerCase()),
  );

  async function act(userId: number, action: string) {
    setBusy(userId);
    await fetch("/api/admin/users", {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ userId, action }),
    });
    setBusy(null);
    router.refresh();
  }

  return (
    <div className="overflow-hidden rounded-2xl border border-line bg-card">
      <div className="border-b border-line p-4">
        <input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search users…"
          className="w-full max-w-sm rounded-xl border border-line bg-panel-2 px-4 py-2.5 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
        />
      </div>
      <div className="overflow-x-auto">
        <table className="w-full min-w-[760px] text-left text-sm">
          <thead>
            <tr className="border-b border-line text-xs uppercase tracking-wider text-[#7c8698]">
              <th className="px-6 py-4 font-medium">User</th>
              <th className="px-6 py-4 font-medium">Role</th>
              <th className="px-6 py-4 font-medium">Status</th>
              <th className="px-6 py-4 text-right font-medium">Balance</th>
              <th className="px-6 py-4 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((u) => (
              <tr key={u.id} className="border-b border-line/60 last:border-0">
                <td className="px-6 py-4">
                  <p className="font-semibold text-white">{u.name}</p>
                  <p className="text-xs text-[#7c8698]">{u.email}</p>
                </td>
                <td className="px-6 py-4">
                  <span
                    className={`rounded-full px-2.5 py-1 text-xs font-semibold ${
                      u.role === "admin"
                        ? "bg-brand/15 text-brand"
                        : "bg-white/10 text-[#c7cedb]"
                    }`}
                  >
                    {u.role}
                  </span>
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
                <td className="px-6 py-4 text-right tabular-nums text-white">
                  {formatUsd(Number(u.balanceUsd))}
                </td>
                <td className="px-6 py-4">
                  {u.id === currentId ? (
                    <span className="block text-right text-xs text-[#7c8698]">
                      You
                    </span>
                  ) : (
                    <div className="flex flex-wrap justify-end gap-2">
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
                      {u.role === "user" ? (
                        <button
                          disabled={busy === u.id}
                          onClick={() => act(u.id, "makeAdmin")}
                          className="rounded-lg border border-line px-3 py-1.5 text-xs font-medium text-brand hover:border-brand disabled:opacity-50"
                        >
                          Make admin
                        </button>
                      ) : (
                        <button
                          disabled={busy === u.id}
                          onClick={() => act(u.id, "makeUser")}
                          className="rounded-lg border border-line px-3 py-1.5 text-xs font-medium text-[#c7cedb] hover:border-white disabled:opacity-50"
                        >
                          Revoke admin
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
