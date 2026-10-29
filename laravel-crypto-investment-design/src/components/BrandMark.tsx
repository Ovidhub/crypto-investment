/**
 * The Actuatrades mark, shared by the marketing header, the footer, the signed-in
 * shell and the app icons. Kept as one component so a future brand refresh only
 * has to touch this file plus `public/brand/mark.png`.
 */
export const BRAND_MARK = "/brand/mark.png";

export default function BrandMark({
  size = 36,
  className = "",
}: {
  size?: number;
  className?: string;
}) {
  return (
    // eslint-disable-next-line @next/next/no-img-element -- fixed local asset, no loader needed
    <img
      src={BRAND_MARK}
      alt=""
      aria-hidden="true"
      width={size}
      height={size}
      style={{ height: size, width: "auto" }}
      className={className}
    />
  );
}
