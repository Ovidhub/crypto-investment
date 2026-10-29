"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import BrandMark from "@/components/BrandMark";

export default function AuthForm({ mode }: { mode: "login" | "register" }) {
  const router = useRouter();
  const isLogin = mode === "login";
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);


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
      const dest =
        data.role === "admin" || data.role === "owner"
          ? "/admin"
          : data.profileComplete === false
            ? "/onboarding"
            : "/dashboard";
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
          <BrandMark size={32} />
          <span className="font-display text-xl font-bold">Actuatrades</span>
        </Link>

        <div className="rounded-3xl border border-line bg-card p-8">
          <h1 className="font-display text-2xl font-bold">
            {isLogin ? "Welcome back" : "Create your account"}
          </h1>
          <p className="mt-1 text-sm text-[#9aa3b2]">
            {isLogin
              ? "Sign in to access your portfolio."
              : "Create your account in minutes and set up your profile."}
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

          <p className="mt-5 text-center text-sm text-[#9aa3b2]">
            {isLogin ? "New to Actuatrades? " : "Already have an account? "}
            <Link
              href={isLogin ? "/register" : "/login"}
              className="font-semibold text-brand hover:underline"
            >
              {isLogin ? "Create an account" : "Sign in"}
            </Link>
          </p>
        </div>

        <p className="mt-6 text-center text-xs text-[#6b7488]">
          Your account is protected by enforced password hashing and rate-limited sign-in.
        </p>
      </div>
    </div>
  );
}
