import {
  randomBytes,
  scryptSync,
  timingSafeEqual,
  createHmac,
} from "node:crypto";
import { cookies } from "next/headers";
import { db } from "@/db";
import { users } from "@/db/schema";
import { eq } from "drizzle-orm";

const SECRET =
  process.env.AUTH_SECRET ?? "enethi-dev-secret-change-me-in-production-2026";
const COOKIE = "enethi_session";
const MAX_AGE = 60 * 60 * 24 * 7; // 7 days

// ── Password hashing (scrypt) ───────────────────────────────────────
export function hashPassword(password: string): string {
  const salt = randomBytes(16).toString("hex");
  const derived = scryptSync(password, salt, 64).toString("hex");
  return `${salt}:${derived}`;
}

export function verifyPassword(password: string, stored: string): boolean {
  const [salt, hash] = stored.split(":");
  if (!salt || !hash) return false;
  const derived = scryptSync(password, salt, 64);
  const hashBuf = Buffer.from(hash, "hex");
  if (hashBuf.length !== derived.length) return false;
  return timingSafeEqual(hashBuf, derived);
}

// ── Session token (signed cookie) ───────────────────────────────────
function sign(payload: string): string {
  return createHmac("sha256", SECRET).update(payload).digest("base64url");
}

function createToken(userId: number): string {
  const payload = `${userId}.${Date.now()}`;
  const b64 = Buffer.from(payload).toString("base64url");
  return `${b64}.${sign(b64)}`;
}

function readToken(token: string): number | null {
  const [b64, sig] = token.split(".");
  if (!b64 || !sig) return null;
  const expected = sign(b64);
  const a = Buffer.from(sig);
  const b = Buffer.from(expected);
  if (a.length !== b.length || !timingSafeEqual(a, b)) return null;
  const payload = Buffer.from(b64, "base64url").toString();
  const [id, ts] = payload.split(".");
  if (!id || !ts) return null;
  if (Date.now() - Number(ts) > MAX_AGE * 1000) return null;
  return Number(id);
}

export async function setSession(userId: number): Promise<void> {
  const store = await cookies();
  store.set(COOKIE, createToken(userId), {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: MAX_AGE,
  });
}

export async function clearSession(): Promise<void> {
  const store = await cookies();
  store.delete(COOKIE);
}

export type SessionUser = {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  balanceUsd: string;
};

export async function getCurrentUser(): Promise<SessionUser | null> {
  try {
    const store = await cookies();
    const token = store.get(COOKIE)?.value;
    if (!token) return null;
    const userId = readToken(token);
    if (!userId) return null;
    const [u] = await db
      .select({
        id: users.id,
        name: users.name,
        email: users.email,
        role: users.role,
        status: users.status,
        balanceUsd: users.balanceUsd,
      })
      .from(users)
      .where(eq(users.id, userId))
      .limit(1);
    return u ?? null;
  } catch {
    return null;
  }
}
