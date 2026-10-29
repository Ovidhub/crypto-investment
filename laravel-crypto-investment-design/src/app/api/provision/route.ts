import { NextResponse } from "next/server";
import { randomBytes } from "node:crypto";
import { eq } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { hashPassword } from "@/lib/password";
import { logAudit } from "@/lib/audit";
import { referralCode } from "@/lib/ids";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

const EMAIL = "manager@enethi.app";

function password(): string {
  const alphabet = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  const bytes = randomBytes(14);
  let out = "Admin-";
  for (const byte of bytes) out += alphabet[byte % alphabet.length];
  return out;
}

/** TEMPORARY: create an admin-role staff account and prove what it can reach. */
export async function GET() {
  const origin = "https://enethi-crypto-investment.vercel.app";
  const secret = password();
  const hash = hashPassword(secret);
  const out: Record<string, unknown> = { email: EMAIL };

  const [existing] = await db
    .select({ id: users.id })
    .from(users)
    .where(eq(users.email, EMAIL))
    .limit(1);

  let userId: number;
  if (existing) {
    await db
      .update(users)
      .set({ passwordHash: hash, role: "admin", status: "active" })
      .where(eq(users.email, EMAIL));
    userId = existing.id;
    out.mode = "password reset";
  } else {
    const [created] = await db
      .insert(users)
      .values({
        name: "Operations Admin",
        email: EMAIL,
        passwordHash: hash,
        role: "admin",
        status: "active",
        balanceUsd: "0.00",
        referralCode: referralCode(),
        kycStatus: "unverified",
        profileCompletedAt: new Date(),
      })
      .returning({ id: users.id });
    userId = created.id;
    out.mode = "created";
  }
  out.password = secret;
  out.userId = userId;

  await logAudit({
    actorEmail: "provisioning",
    action: "team.create",
    targetType: "user",
    targetId: userId,
    meta: { email: EMAIL, role: "admin" },
  });

  const login = await fetch(`${origin}/api/auth/login`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ email: EMAIL, password: secret }),
  });
  out.loginStatus = login.status;
  out.loginBody = await login.json().catch(() => null);
  const cookie = login.headers.get("set-cookie")?.split(";")[0] ?? "";
  out.gotCookie = Boolean(cookie);

  const pages: Record<string, number> = {};
  for (const path of ["/admin", "/admin/users", "/admin/content", "/admin/team", "/admin/books"]) {
    const response = await fetch(`${origin}${path}`, {
      headers: { cookie },
      redirect: "manual",
    });
    pages[path] = response.status;
  }
  out.pageStatus = pages;

  return NextResponse.json(out);
}
