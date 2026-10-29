import { apiUser } from "@/lib/auth";
import { clientKey, rateLimit, tooMany } from "@/lib/rate-limit";
import { getProfile, saveProfile } from "@/lib/profile";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiUser();
  if (!auth.user) return auth.response;
  return Response.json(await getProfile(auth.user.id));
}

export async function POST(req: Request) {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const limited = rateLimit(clientKey(req, `profile:${auth.user.id}`), 12, 60 * 1000);
  if (!limited.ok) return tooMany(limited.retryAfter);

  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return Response.json({ error: "Invalid request." }, { status: 400 });
  }

  const before = await getProfile(auth.user.id);
  const result = await saveProfile({
    userId: auth.user.id,
    actorEmail: auth.user.email,
    raw: (body ?? {}) as Record<string, unknown>,
    firstTime: !before.complete,
  });

  if (!result.ok) {
    return Response.json({ error: "Check the highlighted fields.", errors: result.errors }, { status: 400 });
  }
  return Response.json({ ok: true, message: result.message, complete: true });
}
