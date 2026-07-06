import { db } from "@/db";
import { messages } from "@/db/schema";

export const dynamic = "force-dynamic";

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export async function POST(req: Request) {
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }
  const { name, email, message } = (body ?? {}) as Record<string, unknown>;

  if (typeof name !== "string" || name.trim().length < 2) {
    return Response.json({ error: "Please enter your name." }, { status: 400 });
  }
  if (typeof email !== "string" || !EMAIL_RE.test(email)) {
    return Response.json({ error: "Enter a valid email." }, { status: 400 });
  }
  if (typeof message !== "string" || message.trim().length < 5) {
    return Response.json({ error: "Please enter a message." }, { status: 400 });
  }

  try {
    await db.insert(messages).values({
      name: name.trim().slice(0, 120),
      email: email.trim().toLowerCase().slice(0, 200),
      message: message.trim().slice(0, 2000),
    });
    return Response.json({ ok: true });
  } catch {
    return Response.json({ error: "Failed to send. Try again." }, { status: 500 });
  }
}
