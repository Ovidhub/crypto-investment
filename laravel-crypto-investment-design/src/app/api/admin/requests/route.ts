import { apiAdmin } from "@/lib/auth";
import {
  approveDeposit,
  approveWithdrawal,
  rejectDeposit,
  rejectWithdrawal,
} from "@/lib/wallet";

export const dynamic = "force-dynamic";

const ACTIONS = new Set([
  "approve-deposit",
  "reject-deposit",
  "approve-withdrawal",
  "reject-withdrawal",
]);

export async function POST(req: Request) {
  const auth = await apiAdmin();
  if (!auth.user) return auth.response;

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { action, id, note } = (body ?? {}) as Record<string, unknown>;

  if (typeof action !== "string" || !ACTIONS.has(action)) {
    return Response.json({ error: "Unknown action." }, { status: 400 });
  }
  const requestId = Number(id);
  if (!Number.isInteger(requestId)) {
    return Response.json({ error: "Invalid request id." }, { status: 400 });
  }
  const actor = { id: auth.user.id, email: auth.user.email };
  const reason = typeof note === "string" ? note.trim() : "";

  if (action.startsWith("reject") && reason.length < 3) {
    return Response.json(
      { error: "Add a short reason so the customer knows why." },
      { status: 400 },
    );
  }

  const result =
    action === "approve-deposit"
      ? await approveDeposit(requestId, actor, reason)
      : action === "reject-deposit"
        ? await rejectDeposit(requestId, actor, reason)
        : action === "approve-withdrawal"
          ? await approveWithdrawal(requestId, actor, reason)
          : await rejectWithdrawal(requestId, actor, reason);

  if (!result.ok) {
    return Response.json({ error: result.error }, { status: result.status ?? 400 });
  }
  return Response.json({ ok: true, message: result.message });
}
