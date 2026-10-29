import { NextResponse } from "next/server";
import { db } from "@/db";
import { users } from "@/db/schema";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

/** TEMPORARY: sign up, then sign in with the same credentials. */
export async function GET() {
  const origin = "https://enethi-crypto-investment.vercel.app";
  const stamp = Date.now();
  const email = `probe.${stamp}@example.com`;
  const password = `Probe-${stamp}-pass`;
  const out: Record<string, unknown> = {};

  const register = await fetch(`${origin}/api/auth/register`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ name: "Signup Probe", email, password }),
  }).catch((error: unknown) => ({ status: 0, error } as unknown as Response));
  out.registerStatus = register.status;
  out.registerSetCookie = Boolean(register.headers?.get?.("set-cookie"));
  out.registerBody = await (register as Response).json?.().catch?.(() => null);

  const login = await fetch(`${origin}/api/auth/login`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ email, password }),
  }).catch(() => null);
  out.loginStatus = login?.status ?? 0;
  out.loginSetCookie = Boolean(login?.headers?.get?.("set-cookie"));
  out.loginBody = await login?.json?.().catch?.(() => null);

  try {
    const removed = await db.delete(users).where(eq(users.email, email)).returning({ id: users.id });
    out.cleanedUp = removed.length;
  } catch (error) {
    out.cleanUpError = error instanceof Error ? error.message : String(error);
  }

  return NextResponse.json(out);
}
