import { db } from "@/db";
import { waitlist } from "@/db/schema";
import { sql } from "drizzle-orm";

export const dynamic = "force-dynamic";

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const PLANS = ["starter", "pro", "whale"];

export async function POST(req: Request) {
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request body." }, { status: 400 });
  }

  const { name, email, plan } = (body ?? {}) as Record<string, unknown>;

  if (typeof name !== "string" || name.trim().length < 2) {
    return Response.json({ error: "Please enter your name." }, { status: 400 });
  }
  if (typeof email !== "string" || !EMAIL_RE.test(email)) {
    return Response.json(
      { error: "Please enter a valid email address." },
      { status: 400 },
    );
  }
  const safePlan =
    typeof plan === "string" && PLANS.includes(plan) ? plan : "starter";

  try {
    const [row] = await db
      .insert(waitlist)
      .values({
        name: name.trim().slice(0, 120),
        email: email.trim().toLowerCase().slice(0, 200),
        plan: safePlan,
      })
      .returning({ id: waitlist.id });

    const [{ count }] = await db
      .select({ count: sql<number>`count(*)::int` })
      .from(waitlist);

    return Response.json({ ok: true, id: row.id, total: count });
  } catch {
    return Response.json(
      { error: "Something went wrong. Please try again." },
      { status: 500 },
    );
  }
}

export async function GET() {
  try {
    const [{ count }] = await db
      .select({ count: sql<number>`count(*)::int` })
      .from(waitlist);
    return Response.json({ total: count });
  } catch {
    return Response.json({ total: 0 });
  }
}
