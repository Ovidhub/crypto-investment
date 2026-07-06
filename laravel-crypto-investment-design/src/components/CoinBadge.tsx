export default function CoinBadge({
  symbol,
  color,
  size = 36,
}: {
  symbol: string;
  color: string;
  size?: number;
}) {
  return (
    <span
      className="grid shrink-0 place-items-center rounded-full font-display font-bold"
      style={{
        width: size,
        height: size,
        background: `linear-gradient(135deg, ${color}, ${color}cc)`,
        color: "#05060a",
        fontSize: size * 0.36,
        boxShadow: `0 6px 18px -6px ${color}88`,
      }}
      aria-hidden
    >
      {symbol.slice(0, 3)}
    </span>
  );
}
