import { createHmac, randomBytes, timingSafeEqual } from "node:crypto";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { db, ensureDemoUser, isDemoMode } from "@/db";
import { users } from "@/db/schema";
import { hashPassword, verifyPassword } from "@/lib/password";
import { eq } from "drizzle-orm";

export { hashPassword, verifyPassword };

const SECRET =
  process.env.AUTH_SECRET ??
  // A random per-process secret keeps forged cookies impossible, at the cost of
  // signing everyone out on restart. Set AUTH_SECRET in production.
  randomBytes(32).toString("hex");
const COOKIE = "enethi_session";
const MAX_AGE = 60 * 60 * 24 * 7; // 7 days

export const ROLES = ["user", "support", "admin"] as const;
export type Role = (typeof ROLES)[number];

export type SessionUser = {
  id: number;
  name: string;
  email: string;
  role: string;
  status: string;
  balanceUsd: string;
  referralCode: string;
  kycStatus: string;
  /** ISO timestamp once the onboarding form has been submitted. */
  profileCompletedAt: string | null;
};

/** A DB row may hand us a Date; the cookie always stores an ISO string. */
export type SessionInput = Omit<SessionUser, "profileCompletedAt"> & {
  profileCompletedAt: Date | string | null;
};

type SessionPayload = {
  id: number;
  ts: number;
  user?: SessionUser;
};

function sign(payload: string): string {
  return createHmac("sha256", SECRET).update(payload).digest("base64url");
}

function createToken(user: number | SessionInput): string {
  const payload: SessionPayload =
    typeof user === "number"
      ? { id: user, ts: Date.now() }
      : {
          id: user.id,
          ts: Date.now(),
          user: {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role,
            status: user.status,
            balanceUsd: user.balanceUsd,
            referralCode: user.referralCode,
            kycStatus: user.kycStatus,
            profileCompletedAt: user.profileCompletedAt
              ? new Date(user.profileCompletedAt).toISOString()
              : null,
          },
        };
  const b64 = Buffer.from(JSON.stringify(payload)).toString("base64url");
  return `${b64}.${sign(b64)}`;
}

function readToken(token: string): SessionPayload | null {
  const [b64, sig] = token.split(".");
  if (!b64 || !sig) return null;
  const expected = sign(b64);
  const a = Buffer.from(sig);
  const b = Buffer.from(expected);
  if (a.length !== b.length || !timingSafeEqual(a, b)) return null;

  try {
    const payload = JSON.parse(
      Buffer.from(b64, "base64url").toString("utf8"),
    ) as SessionPayload;
    if (!Number.isInteger(payload.id) || !Number.isFinite(payload.ts)) {
      return null;
    }
    if (Date.now() - payload.ts > MAX_AGE * 1000) return null;
    return payload;
  } catch {
    return null;
  }
}

export async function setSession(user: number | SessionInput): Promise<void> {
  const store = await cookies();
  store.set(COOKIE, createToken(user), {
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

export async function getCurrentUser(): Promise<SessionUser | null> {
  try {
    const store = await cookies();
    const token = store.get(COOKIE)?.value;
    if (!token) return null;
    const session = readToken(token);
    if (!session) return null;

    // Demo mode keeps its data in process memory. Re-seed this user in the
    // current function instance before reading from it. Because each instance
    // assigns its own serial ids, resolve demo users by email rather than by
    // the id embedded in the cookie.
    const useDemoIdentity = isDemoMode && !!session.user;
    if (useDemoIdentity && session.user) {
      await ensureDemoUser(session.user);
    }

    const [u] = await db
      .select({
        id: users.id,
        name: users.name,
        email: users.email,
        role: users.role,
        status: users.status,
        balanceUsd: users.balanceUsd,
        referralCode: users.referralCode,
        kycStatus: users.kycStatus,
        profileCompletedAt: users.profileCompletedAt,
      })
      .from(users)
      .where(
        useDemoIdentity && session.user
          ? eq(users.email, session.user.email)
          : eq(users.id, session.id),
      )
      .limit(1);
    if (!u) return null;
    return {
      ...u,
      profileCompletedAt: u.profileCompletedAt
        ? new Date(u.profileCompletedAt).toISOString()
        : null,
    };
  } catch {
    return null;
  }
}

/** Signed-in and not suspended. */
export async function requireUser(): Promise<SessionUser> {
  const user = await getCurrentUser();
  if (!user) redirect("/login");
  if (user.status === "suspended") redirect("/suspended");
  return user;
}

export function isStaff(role: string): boolean {
  return role === "admin" || role === "support";
}

/** Admin or support. */
export async function requireStaff(): Promise<SessionUser> {
  const user = await requireUser();
  if (!isStaff(user.role)) redirect("/dashboard");
  return user;
}

/** Full admin only — approvals, settings, ledger adjustments. */
export async function requireAdmin(): Promise<SessionUser> {
  const user = await requireUser();
  if (user.role !== "admin") redirect("/dashboard");
  return user;
}

/** API-side guard: returns the user or a 401/403 response. */
export async function apiUser(): Promise<
  { user: SessionUser; response?: never } | { user?: never; response: Response }
> {
  const user = await getCurrentUser();
  if (!user) {
    return {
      response: Response.json({ error: "Please sign in." }, { status: 401 }),
    };
  }
  if (user.status === "suspended") {
    return {
      response: Response.json(
        { error: "This account is suspended." },
        { status: 403 },
      ),
    };
  }
  return { user };
}

export async function apiStaff(): Promise<
  { user: SessionUser; response?: never } | { user?: never; response: Response }
> {
  const result = await apiUser();
  if (!result.user) return result;
  if (!isStaff(result.user.role)) {
    return {
      response: Response.json({ error: "Not allowed." }, { status: 403 }),
    };
  }
  return result;
}

export async function apiAdmin(): Promise<
  { user: SessionUser; response?: never } | { user?: never; response: Response }
> {
  const result = await apiUser();
  if (!result.user) return result;
  if (result.user.role !== "admin") {
    return {
      response: Response.json({ error: "Not allowed." }, { status: 403 }),
    };
  }
  return result;
}
