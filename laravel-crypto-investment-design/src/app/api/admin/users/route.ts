import { eq } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { apiAdmin } from "@/lib/auth";
import { logAudit, notify } from "@/lib/audit";

export const dynamic = "force-dynamic";

const ROLES = new Set(["user", "support", "admin", "owner"]);

export async function PATCH(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { userId, action, role } = (body ?? {}) as Record<string, unknown>;
  const id = Number(userId);
  if (!Number.isInteger(id)) {
    return Response.json({ error: "Invalid user." }, { status: 400 });
  }
  if (id === auth.user.id) {
    return Response.json(
      { error: "You cannot modify your own account." },
      { status: 400 },
    );
  }

  const [target] = await db.select().from(users).where(eq(users.id, id)).limit(1);
  if (!target) return Response.json({ error: "User not found." }, { status: 404 });

  let auditAction = "";
  let message = "";

  if (action === "suspend") {
    if (target.role === "admin" || target.role === "owner") {
      return Response.json(
        { error: "Suspend another admin only after demoting them." },
        { status: 400 },
      );
    }
    await db.update(users).set({ status: "suspended" }).where(eq(users.id, id));
    auditAction = "user.suspend";
    message = "This account has been suspended by an operator.";
  } else if (action === "activate") {
    await db.update(users).set({ status: "active" }).where(eq(users.id, id));
    auditAction = "user.activate";
    message = "Your account has been reactivated.";
  } else if (action === "setRole") {
    const next = typeof role === "string" ? role : "";
    if (!ROLES.has(next)) {
      return Response.json({ error: "Unknown role." }, { status: 400 });
    }
    // Only a super admin may grant or revoke the super admin role.
    if ((next === "owner" || target.role === "owner") && auth.user.role !== "owner") {
      return Response.json(
        { error: "Only the super admin can change the super admin role." },
        { status: 403 },
      );
    }
    if (target.role === "owner" && next !== "owner") {
      const owners = await db
        .select({ id: users.id })
        .from(users)
        .where(eq(users.role, "owner"));
      if (owners.length <= 1) {
        return Response.json(
          { error: "Keep at least one super admin account." },
          { status: 400 },
        );
      }
    }
    if (target.role === "admin" && next !== "admin") {
      // Never allow the last admin to be demoted into a locked-out platform.
      const admins = await db
        .select({ id: users.id })
        .from(users)
        .where(eq(users.role, "admin"));
      if (admins.length <= 1 && next !== "owner") {
        return Response.json(
          { error: "Keep at least one admin account." },
          { status: 400 },
        );
      }
    }
    await db.update(users).set({ role: next }).where(eq(users.id, id));
    auditAction = "user.role";
    message = `Your role was changed to ${next}.`;
  } else {
    return Response.json({ error: "Unknown action." }, { status: 400 });
  }

  await logAudit({
    actorId: auth.user.id,
    actorEmail: auth.user.email,
    action: auditAction,
    targetType: "user",
    targetId: id,
    meta:
      action === "setRole"
        ? { from: target.role, to: role }
        : { status: action === "suspend" ? "suspended" : "active" },
  });
  if (message) {
    await notify({ userId: id, title: "Account update", body: message });
  }

  return Response.json({ ok: true });
}
