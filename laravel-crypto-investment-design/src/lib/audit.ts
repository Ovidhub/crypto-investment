import { db } from "@/db";
import { auditLog, notifications } from "@/db/schema";

/**
 * Append-only operations trail. Every privileged action (approvals, role
 * changes, ledger adjustments, impersonation) must land here with the actor
 * recorded, so an operator can always answer "who did this and when".
 */
export async function logAudit(input: {
  actorId?: number | null;
  actorEmail?: string | null;
  action: string;
  targetType?: string;
  targetId?: number | null;
  meta?: Record<string, unknown>;
}): Promise<void> {
  await db.insert(auditLog).values({
    actorId: input.actorId ?? null,
    actorEmail: input.actorEmail ?? null,
    action: input.action.slice(0, 60),
    targetType: input.targetType ?? null,
    targetId: input.targetId ?? null,
    meta: input.meta ? JSON.stringify(input.meta) : null,
  });
}

export async function notify(input: {
  userId: number;
  title: string;
  body: string;
}): Promise<void> {
  await db.insert(notifications).values({
    userId: input.userId,
    title: input.title.slice(0, 140),
    body: input.body,
  });
}
