"use client";

import { useState } from "react";

export type TeamRow = {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  lastSignIn?: string | null;
};

const ROLES = [
  { value: "support", label: "Support — read-only operations" },
  { value: "admin", label: "Admin — approvals, users, ledger" },
  { value: "owner", label: "Super admin — everything, including content" },
  { value: "user", label: "Customer — dashboard only" },
];

export default function TeamManager({
  rows,
  currentId,
}: {
  rows: TeamRow[];
  currentId: number;
}) {
  const [busy, setBusy] = useState(false);
  const [notice, setNotice] = useState<{ tone: "ok" | "error"; text: string } | null>(
    null,
  );
  const [secret, setSecret] = useState<{ email: string; password: string } | null>(
    null,
  );
  const [form, setForm] = useState({
    name: "",
    email: "",
    role: "admin",
    password: "",
  });
  const [list, setList] = useState(rows);

  async function post(payload: Record<string, unknown>) {
    setBusy(true);
    setNotice(null);
    try {
      const res = await fetch("/api/admin/team", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = (await res.json()) as {
        error?: string;
        message?: string;
        generatedPassword?: string | null;
        userId?: number;
      };
      if (!res.ok) {
        setNotice({ tone: "error", text: data.error ?? "Request failed." });
        return null;
      }
      if (data.message) setNotice({ tone: "ok", text: data.message });
      return data;
    } catch {
      setNotice({ tone: "error", text: "Network error. Try again." });
      return null;
    } finally {
      setBusy(false);
    }
  }

  async function create() {
    const data = await post({ action: "create", ...form });
    if (!data) return;
    if (data.generatedPassword) {
      setSecret({ email: form.email, password: data.generatedPassword });
    } else {
      setNotice({ tone: "ok", text: `${form.email} created.` });
    }
    setList((prev) =>
      prev.length >= 0
        ? [
            ...prev,
            {
              id: data.userId ?? Date.now(),
              name: form.name,
              email: form.email,
              role: form.role,
              status: "active",
            },
          ]
        : prev,
    );
    setForm({ name: "", email: "", role: "admin", password: "" });
  }

  async function setRole(userId: number, role: string) {
    const data = await post({ action: "setRole", userId, role });
    if (data) {
      setList((prev) => prev.map((r) => (r.id === userId ? { ...r, role } : r)));
    }
  }

  async function resetPassword(row: TeamRow) {
    if (!confirm(`Issue a new temporary password for ${row.email}?`)) return;
    const data = await post({ action: "resetPassword", userId: row.id });
    if (data?.generatedPassword) {
      setSecret({ email: row.email, password: data.generatedPassword });
    }
  }

  async function toggleStatus(row: TeamRow) {
    const status = row.status === "active" ? "suspended" : "active";
    const data = await post({ action: "setStatus", userId: row.id, status });
    if (data) {
      setList((prev) => prev.map((r) => (r.id === row.id ? { ...r, status } : r)));
    }
  }

  return (
    <div className="space-y-6">
      {notice && (
        <p
          className={`rounded-xl border px-4 py-2.5 text-sm ${
            notice.tone === "ok"
              ? "border-accent/40 bg-accent/10 text-accent"
              : "border-danger/40 bg-danger/10 text-danger"
          }`}
        >
          {notice.text}
        </p>
      )}

      {secret && (
        <div className="rounded-2xl border border-brand/40 bg-brand/10 p-5">
          <p className="text-sm font-semibold text-white">
            Temporary password for {secret.email}
          </p>
          <p className="mt-2 select-all font-mono text-lg text-white">{secret.password}</p>
          <p className="mt-2 text-xs text-[#c7cedb]">
            Copy it now — it is not stored anywhere and will not be shown again. The
            holder can change it in Settings.
          </p>
          <button
            type="button"
            onClick={() => setSecret(null)}
            className="mt-3 rounded-lg border border-line px-3 py-1.5 text-xs font-semibold text-[#c7cedb] hover:bg-white/5"
          >
            Dismiss
          </button>
        </div>
      )}

      <div className="rounded-2xl border border-line bg-card p-6">
        <h2 className="font-display text-lg font-bold text-white">Create an account</h2>
        <p className="mt-1 text-xs text-[#9aa3b2]">
          Leave the password empty to generate a strong temporary one you can hand over.
        </p>
        <div className="mt-4 grid gap-3 sm:grid-cols-2">
          <input
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            placeholder="Full name"
            className="rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
          />
          <input
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            placeholder="email@example.com"
            className="rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
          />
          <select
            value={form.role}
            onChange={(e) => setForm({ ...form, role: e.target.value })}
            className="rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white"
          >
            {ROLES.map((r) => (
              <option key={r.value} value={r.value}>
                {r.label}
              </option>
            ))}
          </select>
          <input
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
            placeholder="Password (optional, 10+ characters)"
            className="rounded-xl border border-line bg-panel-2 px-3 py-2 text-sm text-white outline-none focus:border-brand"
          />
        </div>
        <button
          type="button"
          onClick={create}
          disabled={busy || !form.name || !form.email}
          className="mt-4 rounded-full bg-gradient-to-r from-brand to-brand-2 px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
        >
          {busy ? "Working…" : "Create account"}
        </button>
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        <table className="w-full text-left text-sm">
          <thead className="bg-panel-2 text-xs uppercase tracking-wide text-[#7c8698]">
            <tr>
              <th className="px-4 py-3">Person</th>
              <th className="px-4 py-3">Role</th>
              <th className="px-4 py-3">Status</th>
              <th className="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {list.map((row) => (
              <tr key={row.id} className="border-t border-line">
                <td className="px-4 py-3">
                  <p className="font-semibold text-white">{row.name}</p>
                  <p className="text-xs text-[#7c8698]">{row.email}</p>
                </td>
                <td className="px-4 py-3">
                  {row.id === currentId ? (
                    <span className="text-xs text-[#9aa3b2]">You · {row.role}</span>
                  ) : (
                    <select
                      value={row.role}
                      onChange={(e) => setRole(row.id, e.target.value)}
                      disabled={busy}
                      className="rounded-lg border border-line bg-panel-2 px-2 py-1.5 text-xs text-white"
                    >
                      {["user", "support", "admin", "owner"].map((r) => (
                        <option key={r} value={r}>
                          {r}
                        </option>
                      ))}
                    </select>
                  )}
                </td>
                <td className="px-4 py-3">
                  <span
                    className={`rounded-full px-2 py-0.5 text-[11px] font-semibold ${
                      row.status === "active"
                        ? "bg-accent/15 text-accent"
                        : "bg-danger/15 text-danger"
                    }`}
                  >
                    {row.status}
                  </span>
                </td>
                <td className="px-4 py-3 text-right">
                  <div className="inline-flex gap-2">
                    <button
                      type="button"
                      onClick={() => resetPassword(row)}
                      disabled={busy}
                      className="rounded-lg border border-line px-2.5 py-1.5 text-[11px] font-semibold text-[#c7cedb] hover:bg-white/5"
                    >
                      Reset password
                    </button>
                    {row.id !== currentId && (
                      <button
                        type="button"
                        onClick={() => toggleStatus(row)}
                        disabled={busy}
                        className={`rounded-lg border px-2.5 py-1.5 text-[11px] font-semibold ${
                          row.status === "active"
                            ? "border-danger/40 text-danger hover:bg-danger/10"
                            : "border-accent/40 text-accent hover:bg-accent/10"
                        }`}
                      >
                        {row.status === "active" ? "Suspend" : "Restore"}
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
