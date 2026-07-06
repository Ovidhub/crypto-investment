"use client";

import { useState } from "react";

export default function ContactForm() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);
  const [msg, setMsg] = useState<{ type: "ok" | "err"; text: string } | null>(
    null,
  );

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMsg(null);
    try {
      const res = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, email, message }),
      });
      const data = await res.json();
      if (!res.ok) {
        setMsg({ type: "err", text: data.error ?? "Failed to send." });
        setLoading(false);
        return;
      }
      setMsg({ type: "ok", text: "Thanks! We'll get back to you shortly." });
      setName("");
      setEmail("");
      setMessage("");
      setLoading(false);
    } catch {
      setMsg({ type: "err", text: "Network error. Try again." });
      setLoading(false);
    }
  }

  return (
    <form onSubmit={submit} className="rounded-3xl border border-line bg-card p-7">
      <div className="space-y-3">
        <input
          value={name}
          onChange={(e) => setName(e.target.value)}
          placeholder="Full name"
          className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
        />
        <input
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          type="email"
          placeholder="you@email.com"
          className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
        />
        <textarea
          value={message}
          onChange={(e) => setMessage(e.target.value)}
          placeholder="How can we help?"
          rows={5}
          className="w-full resize-none rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
        />
      </div>

      {msg && (
        <p
          className={`mt-3 text-sm ${msg.type === "ok" ? "text-accent" : "text-[#f6465d]"}`}
        >
          {msg.text}
        </p>
      )}

      <button
        type="submit"
        disabled={loading}
        className="mt-4 w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-6 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.01] disabled:opacity-60"
      >
        {loading ? "Sending…" : "Send message"}
      </button>
    </form>
  );
}
