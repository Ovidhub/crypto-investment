import { fetchLiveCoins } from "@/lib/coins";

export const dynamic = "force-dynamic";

export async function GET() {
  const coins = await fetchLiveCoins();
  return Response.json(
    { coins, updatedAt: new Date().toISOString() },
    {
      headers: {
        "Cache-Control": "public, s-maxage=60, stale-while-revalidate=120",
      },
    },
  );
}
