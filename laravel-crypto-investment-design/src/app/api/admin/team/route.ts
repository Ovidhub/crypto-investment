import { randomBytes } from "node:crypto";
import { eq } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { apiOwner, ROLES, type Role } from "@/lib/auth";
import { hashPassword } from "@/lib/password";
import { logAudit, notify } from "@/lib/audit";
import { referralCode } from "@/lib/ids";
import { rateLimit } from "@/lib/rate-limit";

export const dynamic = "force-dynamic";

const ASSIGNABLE: Role[] = ["user", "support", "admin", "owner"];

/** Readable password the super admin can hand over. */
function tempPassword(): string {
  const alphabet = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  const bytes = randomBytes(16);
  let out = "";
  for (let i = 0; i < 16; i += 1) out += alphabet[bytes[i] % alphabet.length];
  return out;
}

/**
 * Super admin staff management: create an account, change a role, or issue a
 * fresh temporary password.
 */
export async function POST(req: Request) {
  const auth = await apiOwner();
  if (!auth.user) return auth.response;

  const limited = rateLimit(`team:${auth.user.id}`, 30, 60_000);
  if (!limited.ok) {
    return Response.json(
      { error: "Too many changes. Try again in a minute." },
      { status: 429 },
    );
  }

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const input = (body ?? {}) as Record<string, unknown>;
  const action = typeof input.action === "string" ? input.action : "";

  if (action === "create") {
    const name = typeof input.name === "string" ? input.name.trim().slice(0, 120) : "";
    const email =
      typeof input.email === "string"
        ? input.email.trim().toLowerCase().slice(0, 200)
        : "";
    const role = (typeof input.role === "string" ? input.role : "user") as Role;
    const password =
      typeof input.password === "string" && input.password.length >= 10
        ? input.password
        : tempPassword();

    if (name.length < 2) {
      return Response.json({ error: "Enter a name." }, { status: 400 });
    }
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      return Response.json({ error: "Enter a valid email address." }, { status: 400 });
    }
    if (!ASSIGNABLE.includes(role)) {
      return Response.json({ error: "Unknown role." }, { status: 400 });
    }

    const [existing] = await db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.email, email))
      .limit(1);
    if (existing) {
      return Response.json(
        { error: "An account with this email already exists." },
        { status: 409 },
      );
    }

    const [created] = await db
      .insert(users)
      .values({
        name,
        email,
        passwordHash: hashPassword(password),
        role,
        status: "active",
        balanceUsd: "0.00",
        referralCode: referralCode(),
        kycStatus: "unverified",
        // Staff accounts skip the customer onboarding gate.
        profileCompletedAt: role === "user" ? null : new Date(),
      })
      .returning({ id: users.id });

    await logAudit({
      actorId: auth.user.id,
      actorEmail: auth.user.email,
      action: "team.create",
      targetType: "user",
      targetId: created.id,
      meta: { email, role },
    });
    if (role !== "owner") {
      await notify({
        userId: created.id,
        title: "Welcome to the team",
        body: "Your staff account was created. Change the temporary password from Settings.",
      });
    }

    return Response.json({
      ok: true,
      userId: created.id,
      generatedPassword: typeof input.password === "string" ? null : password,
    });
  }

  const userId = Number(input.userId);
  if (!Number.isInteger(userId)) {
    return Response.json({ error: "Invalid user." }, { status: 400 });
  }
  const [target] = await db.select().from(users).where(eq(users.id, userId)).limit(1);
  if (!target) return Response.json({ error: "User not found." }, { status: 404 });

  if (action === "setRole") {
    const role = (typeof input.role === "string" ? input.role : "") as Role;
    if (!ASSIGNABLE.includes(role)) {
      return Response.json({ error: "Unknown role." }, { status: 400 });
    }
    if (userId === auth.user.id) {
      return Response.json(
        { error: "You cannot change your own role." },
        { status: 400 },
      );
    }
    if (target.role === "owner" && role !== "owner") {
      const owners = await db
        .select({ id: users.id })
        .from(users)
        .where(eq(users.role, "owner"));
      if (owners.length <= 1) {
        return Response.json(
          { error: "Keep at least one super admin." },
          { status: 400 },
        );
      }
    }
    await db.update(users).set({ role }).where(eq(users.id, userId));
    await logAudit({
      actorId: auth.user.id,
      actorEmail: auth.user.email,
      action: "team.role",
      targetType: "user",
      targetId: userId,
      meta: { from: target.role, to: role },
    });
    return Response.json({ ok: true, message: `${target.name} is now ${role}.` });
  }

  if (action === "resetPassword") {
    const password =
      typeof input.password === "string" && input.password.length >= 10
        ? input.password
        : tempPassword();
    await db
      .update(users)
      .set({ passwordHash: hashPassword(password) })
      .where(eq(users.id, userId));
    await logAudit({
      actorId: auth.user.id,
      actorEmail: auth.user.email,
      action: "team.reset_password",
      targetType: "user",
      targetId: userId,
    });
    await notify({
      userId,
      title: "Password reset",
      body: "An operator issued a new password for your account. Sign in with it and change it from Settings.",
    });
    return Response.json({
      ok: true,
      generatedPassword: typeof input.password === "string" ? null : password,
      message: `New password set for ${target.name}.`,
    });
  }

  if (action === "setStatus") {
    const status = input.status === "suspended" ? "suspended" : "active";
    if (userId === auth.user.id) {
      return Response.json(
        { error: "You cannot suspend your own account." },
        { status: 400 },
      );
    }
    if (status === "suspended" && (target.role === "owner" || target.role === "admin")) {
      const peers = await db
        .select({ id: users.id })
        .from(users)
        .where(eq(users.role, target.role));
      if (peers.length <= 1) {
        return Response.json(
          { error: "Keep at least one active super admin." },
          { status: 400 },
        );
      }
    }
    await db.update(users).set({ status }).where(eq(users.id, userId));
    await logAudit({
      actorId: auth.user.id,
      actorEmail: auth.user.email,
      action: "team.status",
      targetType: "user",
      targetId: userId,
      meta: { status },
    });
    return Response.json({ ok: true, message: `${target.name} is now ${status}.` });
  }

  return Response.json(
    { error: `Unknown action. Known roles: ${ROLES.join(", ")}.` },
    { status: 400 },
  );
}
