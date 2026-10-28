import { apiUser } from "@/lib/auth";
import { logAudit } from "@/lib/audit";
import { getActivity } from "@/lib/portfolio";
import { userBalances } from "@/lib/ledger";
import { centsToUsd } from "@/lib/money";

export const dynamic = "force-dynamic";

/** RFC 4180-ish escaping: quote fields containing commas, quotes or newlines. */
function cell(value: string | number): string {
  const text = String(value);
  return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
}

export async function GET() {
  const auth = await apiUser();
  if (!auth.user) return auth.response;

  const [activity, balances] = await Promise.all([
    getActivity(auth.user.id, 500),
    userBalances(auth.user.id),
  ]);

  const lines: string[] = [
    "date,type,detail,amount_usd,direction",
    ...activity.map((a) =>
      [
        new Date(a.createdAt).toISOString(),
        a.label,
        a.detail,
        a.usd.toFixed(2),
        a.direction,
      ]
        .map(cell)
        .join(","),
    ),
    "",
    "# Closing balances (simulated)",
    `# Available cash,${centsToUsd(balances.cash)}`,
    `# Crypto at cost,${centsToUsd(balances.crypto)}`,
    `# Locked principal,${centsToUsd(balances.locked)}`,
    `# Stake in open trades,${centsToUsd(balances.stake)}`,
    "# All figures are simulated. No real money is held or transferred.",
  ];

  await logAudit({
    actorId: auth.user.id,
    action: "account.statement",
    targetType: "user",
    targetId: auth.user.id,
  });

  return new Response(lines.join("\n"), {
    headers: {
      "content-type": "text/csv; charset=utf-8",
      "content-disposition": `attachment; filename="enethi-statement-${auth.user.id}.csv"`,
      "cache-control": "no-store",
    },
  });
}
