export type Coin = {
  id: string;
  rank: number;
  name: string;
  symbol: string;
  price: number;
  change24h: number;
  marketCap: number;
  volume24h: number;
  sparkline: number[];
  color: string;
  category: "Bitcoin" | "Ethereum" | "DeFi" | "Layer 1" | "Stablecoin" | "Meme";
};

/** Deterministic pseudo sparkline so SSR + client render match. */
function sparkline(seed: number, trendUp: boolean): number[] {
  const points: number[] = [];
  let v = 50 + (seed % 20);
  for (let i = 0; i < 24; i++) {
    const wave = Math.sin((i + seed) / 2.4) * 6;
    const drift = (trendUp ? 1 : -1) * i * 0.6;
    const noise = ((seed * (i + 3)) % 11) - 5;
    v = Math.max(6, 50 + wave + drift + noise);
    points.push(Number(v.toFixed(2)));
  }
  return points;
}

// Realistic fallback data used when the live market API is unreachable.
export const FALLBACK_COINS: Coin[] = [
  { id: "bitcoin", rank: 1, name: "Bitcoin", symbol: "BTC", price: 83411.24, change24h: 1.77, marketCap: 1_643_000_000_000, volume24h: 42_100_000_000, color: "#f7931a", category: "Bitcoin", sparkline: sparkline(3, true) },
  { id: "ethereum", rank: 2, name: "Ethereum", symbol: "ETH", price: 2605.95, change24h: 2.04, marketCap: 313_000_000_000, volume24h: 18_400_000_000, color: "#627eea", category: "Ethereum", sparkline: sparkline(7, true) },
  { id: "tether", rank: 3, name: "Tether", symbol: "USDT", price: 1.0, change24h: 0.01, marketCap: 141_000_000_000, volume24h: 55_000_000_000, color: "#26a17b", category: "Stablecoin", sparkline: sparkline(11, true) },
  { id: "binancecoin", rank: 4, name: "BNB", symbol: "BNB", price: 592.31, change24h: -0.86, marketCap: 86_000_000_000, volume24h: 1_900_000_000, color: "#f3ba2f", category: "Layer 1", sparkline: sparkline(5, false) },
  { id: "solana", rank: 5, name: "Solana", symbol: "SOL", price: 116.45, change24h: 3.42, marketCap: 55_000_000_000, volume24h: 3_200_000_000, color: "#14f195", category: "Layer 1", sparkline: sparkline(9, true) },
  { id: "ripple", rank: 6, name: "XRP", symbol: "XRP", price: 2.18, change24h: -1.24, marketCap: 124_000_000_000, volume24h: 4_500_000_000, color: "#23292f", category: "DeFi", sparkline: sparkline(13, false) },
  { id: "cardano", rank: 7, name: "Cardano", symbol: "ADA", price: 0.63, change24h: 2.91, marketCap: 22_000_000_000, volume24h: 720_000_000, color: "#0033ad", category: "Layer 1", sparkline: sparkline(2, true) },
  { id: "dogecoin", rank: 8, name: "Dogecoin", symbol: "DOGE", price: 0.168, change24h: 4.15, marketCap: 24_000_000_000, volume24h: 1_600_000_000, color: "#c2a633", category: "Meme", sparkline: sparkline(17, true) },
  { id: "polkadot", rank: 9, name: "Polkadot", symbol: "DOT", price: 3.97, change24h: 2.8, marketCap: 6_100_000_000, volume24h: 210_000_000, color: "#e6007a", category: "Layer 1", sparkline: sparkline(6, true) },
  { id: "chainlink", rank: 10, name: "Chainlink", symbol: "LINK", price: 14.31, change24h: -0.54, marketCap: 9_000_000_000, volume24h: 480_000_000, color: "#2a5ada", category: "DeFi", sparkline: sparkline(8, false) },
  { id: "litecoin", rank: 11, name: "Litecoin", symbol: "LTC", price: 82.77, change24h: 1.12, marketCap: 6_300_000_000, volume24h: 390_000_000, color: "#345d9d", category: "Layer 1", sparkline: sparkline(4, true) },
  { id: "uniswap", rank: 12, name: "Uniswap", symbol: "UNI", price: 7.42, change24h: 3.05, marketCap: 4_400_000_000, volume24h: 180_000_000, color: "#ff007a", category: "DeFi", sparkline: sparkline(14, true) },
];

type GeckoMarket = {
  id: string;
  symbol: string;
  name: string;
  current_price: number;
  price_change_percentage_24h: number | null;
  market_cap: number;
  total_volume: number;
  market_cap_rank: number;
  sparkline_in_7d?: { price: number[] };
};

const COLOR_MAP: Record<string, string> = {
  bitcoin: "#f7931a",
  ethereum: "#627eea",
  tether: "#26a17b",
  binancecoin: "#f3ba2f",
  solana: "#14f195",
  ripple: "#23292f",
  cardano: "#0033ad",
  dogecoin: "#c2a633",
  polkadot: "#e6007a",
  chainlink: "#2a5ada",
  litecoin: "#345d9d",
  "uniswap": "#ff007a",
};

function categoryFor(id: string): Coin["category"] {
  if (id === "bitcoin") return "Bitcoin";
  if (id === "ethereum") return "Ethereum";
  if (id === "tether" || id === "usd-coin") return "Stablecoin";
  if (id === "dogecoin" || id === "shiba-inu") return "Meme";
  if (["uniswap", "chainlink", "ripple", "aave"].includes(id)) return "DeFi";
  return "Layer 1";
}

export async function fetchLiveCoins(): Promise<Coin[]> {
  try {
    const url =
      "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=12&page=1&sparkline=true&price_change_percentage=24h";
    const res = await fetch(url, {
      headers: { accept: "application/json" },
      next: { revalidate: 60 },
    });
    if (!res.ok) throw new Error(`gecko ${res.status}`);
    const data = (await res.json()) as GeckoMarket[];
    if (!Array.isArray(data) || data.length === 0) throw new Error("empty");

    return data.map((c, i) => {
      const prices = c.sparkline_in_7d?.price ?? [];
      const stride = Math.max(1, Math.floor(prices.length / 24));
      const spark =
        prices.length > 0
          ? prices.filter((_, idx) => idx % stride === 0).slice(-24)
          : sparkFromCoin(i, (c.price_change_percentage_24h ?? 0) >= 0);
      return {
        id: c.id,
        rank: c.market_cap_rank ?? i + 1,
        name: c.name,
        symbol: c.symbol.toUpperCase(),
        price: c.current_price ?? 0,
        change24h: c.price_change_percentage_24h ?? 0,
        marketCap: c.market_cap ?? 0,
        volume24h: c.total_volume ?? 0,
        color: COLOR_MAP[c.id] ?? "#8b93a7",
        category: categoryFor(c.id),
        sparkline: spark,
      };
    });
  } catch {
    return FALLBACK_COINS;
  }
}

function sparkFromCoin(i: number, up: boolean) {
  return sparkline(i + 1, up);
}
