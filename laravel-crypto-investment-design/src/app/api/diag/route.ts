import { NextResponse } from "next/server";
import { db } from "@/db";
import { users } from "@/db/schema";
import { DEMO_ACCOUNT } from "@/lib/demo-access";
import { eq } from "drizzle-orm";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

function cap<T>(promise: Promise<T>, ms: number, label: string) {
  return Promise.race([
    promise,
    new Promise<never>((_, reject) =>
      setTimeout(() => reject(new Error(`${label} exceeded ${ms}ms`)), ms),
    ),
  ]);
}

/** TEMPORARY: end-to-end sign-in check against the deployed login route. */
export async function GET() {
  const out: Record<string, unknown> = {};
  const origin = "https://enethi-crypto-investment.vercel.app";

  try {
    const response = await cap(
      fetch(`${origin}/api/auth/login`, {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({
          email: DEMO_ACCOUNT.email,
          password: DEMO_ACCOUNT.password,
        }),
      }),
      20_000,
      "login fetch",
    );
    out.reviewerLogin = {
      status: response.status,
      body: await response.json().catch(() => null),
      setCookie: Boolean(response.headers.get("set-cookie")),
    };
  } catch (error) {
    out.reviewerLogin = {
      error: error instanceof Error ? `${error.name}: ${error.message}` : String(error),
    };
  }

  try {
    const [owner] = await db
      .select({ email: users.email, role: users.role, status: users.status })
      .from(users)
      .where(eq(users.role, "owner"))
      .limit(1);
    out.owner = owner
      ? {
          emailDomain: owner.email.split("@")[1] ?? "",
          role: owner.role,
          status: owner.status,
        }
      : null;
  } catch (error) {
    out.ownerError = error instanceof Error ? error.message : String(error);
  }

  return NextResponse.json(out);
}
