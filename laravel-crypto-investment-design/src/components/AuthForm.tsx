"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

export default function AuthForm({
  mode,
  demo,
}: {
  mode: "login" | "register";
  /** Demo credentials to advertise in demo mode; omitted otherwise. */
  demo?: { email: string; password: string };
}) {
  const router = useRouter();
  const isLogin = mode === "login";
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [demoBusy, setDemoBusy] = useState(false);

  async function signInAsDemo() {
    setDemoBusy(true);
    setError("");
    try {
      const res = await fetch("/api/auth/demo", { method: "POST" });
      const data = (await res.json()) as { error?: string; role?: string };
      if (!res.ok) {
        setError(data.error ?? "Could not open the demo account.");
        setDemoBusy(false);
        return;
      }
      router.push(data.role === "admin" ? "/admin" : "/dashboard");
      router.refresh();
    } catch {
      setError("Network error. Please try again.");
      setDemoBusy(false);
    }
  }

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      const endpoint = isLogin ? "/api/auth/login" : "/api/auth/register";
      const payload = isLogin ? { email, password } : { name, email, password };
      const res = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) {
        setError(data.error ?? "Something went wrong.");
        setLoading(false);
        return;
      }
      const dest = data.role === "admin" ? "/admin" : "/dashboard";
      router.push(dest);
      router.refresh();
    } catch {
      setError("Network error. Please try again.");
      setLoading(false);
    }
  }

  return (
    <div className="relative flex min-h-screen items-center justify-center px-5 py-16">
      <div className="pointer-events-none absolute inset-0 -z-10">
        <div className="absolute inset-0 grid-bg" />
        <div className="absolute left-1/2 top-0 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-brand-2/25 blur-[130px]" />
      </div>

      <div className="w-full max-w-md">
        <Link href="/" className="mb-8 flex items-center justify-center gap-2.5">
          <span className="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand to-brand-2 font-display text-lg font-bold text-ink">
            E
          </span>
          <span className="font-display text-xl font-bold">Enethi</span>
        </Link>

        <div className="rounded-3xl border border-line bg-card p-8">
          <h1 className="font-display text-2xl font-bold">
            {isLogin ? "Welcome back" : "Create your account"}
          </h1>
          <p className="mt-1 text-sm text-[#9aa3b2]">
            {isLogin
              ? "Sign in to access your portfolio."
              : "Start investing in minutes — $10,000 demo balance included."}
          </p>

          <form onSubmit={submit} className="mt-6 space-y-3">
            {!isLogin && (
              <input
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Full name"
                className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
              />
            )}
            <input
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              type="email"
              placeholder="you@email.com"
              autoComplete="email"
              className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
            />
            <input
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              type="password"
              placeholder="Password"
              autoComplete={isLogin ? "current-password" : "new-password"}
              className="w-full rounded-xl border border-line bg-panel-2 px-4 py-3 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand"
            />

            {error && <p className="text-sm text-[#f6465d]">{error}</p>}

            <button
              type="submit"
              disabled={loading}
              className="w-full rounded-xl bg-gradient-to-r from-brand to-brand-2 px-6 py-3.5 text-sm font-semibold text-white transition-transform hover:scale-[1.01] disabled:opacity-60"
            >
              {loading
                ? "Please wait…"
                : isLogin
                  ? "Sign In"
                  : "Create Account"}
            </button>
          </form>

          {isLogin && demo && (
            <div className="mt-6 rounded-2xl border border-brand/25 bg-brand/5 p-4">
              <p className="text-xs font-semibold text-white">User demo account</p>
              <p className="mt-1 text-[11px] leading-relaxed text-[#9aa3b2]">
                A funded, non-admin customer account so you can see the user side —
                wallet, spot trading, the simulated trade desk and statements.
              </p>
              <div className="mt-2 space-y-1 font-mono text-[11px] text-[#c7cedb]">
                <p>{demo.email}</p>
                <p>{demo.password}</p>
              </div>
              <div className="mt-3 flex flex-wrap gap-2">
                <button
                  type="button"
                  onClick={signInAsDemo}
                  disabled={demoBusy}
                  className="rounded-lg bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-white/15 disabled:opacity-50"
                >
                  {demoBusy ? "Opening…" : "Sign in as demo user"}
                </button>
                <button
                  type="button"
                  onClick={() => {
                    setEmail(demo.email);
                    setPassword(demo.password);
                  }}
                  className="rounded-lg border border-line px-3 py-1.5 text-[11px] font-semibold text-[#c7cedb] hover:border-brand hover:text-white"
                >
                  Fill the form instead
                </button>
              </div>
            </div>
          )}

          <p className="mt-5 text-center text-sm text-[#9aa3b2]">
            {isLogin ? "New to Enethi? " : "Already have an account? "}
            <Link
              href={isLogin ? "/register" : "/login"}
              className="font-semibold text-brand hover:underline"
            >
              {isLogin ? "Create an account" : "Sign in"}
            </Link>
          </p>
        </div>

        <p className="mt-6 text-center text-xs text-[#6b7488]">
          Simulation only — no real funds, no card details, nothing leaves the sandbox.
        </p>
      </div>
    </div>
  );
}
