import { apiUser } from "@/lib/auth";
import { exportUserData } from "@/lib/accounts";
import { logAudit } from "@/lib/audit";

export const dynamic = "force-dynamic";

export async function GET() {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const data = await exportUserData(auth.user.id);
  await logAudit({
    actorId: auth.user.id,
    action: "account.export",
    targetType: "user",
    targetId: auth.user.id,
  });

  return new Response(JSON.stringify(data, null, 2), {
    headers: {
      "content-type": "application/json; charset=utf-8",
      "content-disposition": `attachment; filename="actuatrades-data-${auth.user.id}.json"`,
      "cache-control": "no-store",
    },
  });
}
