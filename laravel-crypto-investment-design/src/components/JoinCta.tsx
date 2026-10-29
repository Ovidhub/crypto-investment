"use client";

import { useState } from "react";
import Accent from "./Accent";

const PLANS = [
  { id: "starter", name: "Starter", desc: "For first-time investors" },
  { id: "pro", name: "Pro", desc: "Advanced trading tools" },
  { id: "whale", name: "Whale", desc: "Institutional grade" },
];

type Status = "idle" | "loading" | "success" | "error";

type Copy = {
  title: string;
  subtitle: string;
  button: string;
  success: string;
};

export default function JoinCta({
  initialTotal,
  copy,
}: {
  initialTotal: number;
  copy: Copy;
}) {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [plan, setPlan] = useState("starter");
  const [status, setStatus] = useState<Status>("idle");
  const [message, setMessage] = useState("");
  const [total, setTotal] = useState(initialTotal);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setStatus("loading");
    setMessage("");
    try {
      const res = await fetch("/api/waitlist", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, email, plan }),
      });
      const data = await res.json();
      if (!res.ok) {
        setStatus("error");
        setMessage(data.error ?? "Something went wrong.");
        return;
      }
      setStatus("success");
      setMessage(copy.success);
      setName("");
      setEmail("");
      // The displayed count includes registered accounts, so just add this
      // sign-up rather than trusting the waitlist-only total.
      setTotal((current) => current + 1);
    } catch {
      setStatus("error");
      setMessage("Network error. Please try again.");
    }
  }

  return (
    <section id="join" className="mx-auto max-w-7xl px-5 py-20 sm:px-8">
      <div className="relative overflow-hidden rounded-[2.5rem] border border-line bg-panel p-8 sm:p-14">
        <div className="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-brand-2/30 blur-[100px]" />
        <div className="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-accent/20 blur-[110px]" />

        <div className="relative grid items-center gap-10 lg:grid-cols-2">
          <div>
            <h2 className="font-display text-[clamp(2rem,4vw,3.2rem)] font-bold leading-tight">
              <Accent text={copy.title} />
            </h2>
            <p className="mt-4 max-w-md text-sm leading-relaxed text-[#9aa3b2]">
              {copy.subtitle}
            </p>
            <div className="mt-6 flex items-center gap-3">
              <div className="flex -space-x-2">
                {["#f7931a", "#627eea", "#14f195"].map((c, i) => (
                  <span
                    key={i}
                    className="h-8 w-8 rounded-full border-2 border-panel"
                    style={{ background: c }}
                  />
                ))}
              </div>
              <p className="text-sm text-[#c7cedb]">
                <span className="font-bold text-white">
                  {total.toLocaleString()}+
                </span>{" "}
                investors already joined
              </p>
            </div>
          </div>

          <form
            onSubmit={submit}
            className="rounded-3xl border border-line bg-card p-6"
          >
            <div className="grid gap-3 sm:grid-cols-3">
              {PLANS.map((p) => (
                <button
                  type="button"
                  key={p.id}
                  onClick={() => setPlan(p.id)}
                  className={`rounded-2xl border p-3 text-left transition-colors ${
                    plan === p.id
                      ? "border-brand bg-brand/10"
                      : "border-line bg-panel-2 hover:border-brand/40"
                  }`}
                >
                  <p className="text-sm font-semibold text-white">{p.name}</p>
                  <p className="mt-0.5 text-[11px] text-[#7c8698]">{p.desc}</p>
                </button>
              ))}
            </div>

            <div className="mt-4 space-y-3">
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
            </div>

            <button
              type="submit"
              disabled={status === "loading"}
              className="mt-4 w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-6 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.01] disabled:opacity-60"
            >
              {status === "loading" ? "Joining…" : copy.button}
            </button>

            {message && (
              <p
                className={`mt-3 text-center text-sm ${
                  status === "success" ? "text-accent" : "text-[#f6465d]"
                }`}
              >
                {message}
              </p>
            )}
            <p className="mt-3 text-center text-[11px] text-[#6b7488]">
              No credit card required. Cancel anytime.
            </p>
          </form>
        </div>
      </div>
    </section>
  );
}
