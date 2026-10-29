/**
 * Money utilities.
 *
 * All monetary arithmetic happens in integer minor units (US cents) so that
 * balances, fees and earnings never accumulate floating-point drift. Values are
 * only converted to decimals at the database and presentation boundaries.
 */

export type Cents = bigint;

const ZERO = BigInt(0);
const HUNDRED = BigInt(100);

/** Parse a decimal string/number of USD into cents (half-up on 2dp input). */
export function toCents(value: string | number | bigint | null | undefined): Cents {
  if (value === null || value === undefined) return ZERO;
  if (typeof value === "bigint") return value;

  const raw = typeof value === "number" ? value.toString() : value.trim();
  if (!raw) return ZERO;

  const match = /^(-)?(\d+)(?:\.(\d+))?$/.exec(raw);
  if (!match) {
    const asNumber = Number(raw);
    if (!Number.isFinite(asNumber)) return ZERO;
    return BigInt(Math.round(asNumber * 100));
  }

  const negative = match[1] === "-";
  const whole = BigInt(match[2] || "0");
  const fractionRaw = match[3] ?? "";
  const fraction = fractionRaw.slice(0, 2).padEnd(2, "0");
  const remainder = fractionRaw.length > 2 ? Number(fractionRaw[2]) : 0;
  let cents = whole * HUNDRED + BigInt(fraction || "0");
  if (remainder >= 5) cents += BigInt(1);
  return negative ? -cents : cents;
}

/** Format cents as a plain decimal string suitable for a numeric(18,2) column. */
export function centsToUsd(cents: Cents): string {
  const negative = cents < ZERO;
  const abs = negative ? -cents : cents;
  const whole = abs / HUNDRED;
  const fraction = (abs % HUNDRED).toString().padStart(2, "0");
  return `${negative ? "-" : ""}${whole.toString()}.${fraction}`;
}

/** Convert cents to a JS number for presentation only. */
export function centsToNumber(cents: Cents): number {
  return Number(centsToUsd(cents));
}

/** Parse user input; returns null for anything that is not a valid 2dp amount. */
export function parseUsdInput(input: string): Cents | null {
  const trimmed = input.trim();
  if (!/^\d{1,12}(\.\d{1,2})?$/.test(trimmed)) return null;
  return toCents(trimmed);
}

/** Apply a basis-point rate (1 bp = 0.01%) to an amount, rounding halves up. */
export function applyBasisPoints(amount: Cents, basisPoints: number): Cents {
  if (!Number.isInteger(basisPoints) || basisPoints < 0) return ZERO;
  const numerator = amount * BigInt(basisPoints);
  const denominator = BigInt(10_000);
  const quotient = numerator / denominator;
  const remainder = numerator % denominator;
  return remainder * BigInt(2) >= denominator ? quotient + BigInt(1) : quotient;
}

/** Formats basis points as a human percentage, e.g. 45 -> "0.45%". */
export function formatBasisPoints(basisPoints: number): string {
  return `${(basisPoints / 100).toFixed(2)}%`;
}

export const MIN_DEPOSIT_CENTS = BigInt(1_000); // $10
export const MIN_WITHDRAWAL_CENTS = BigInt(1_000); // $10
export const MAX_AMOUNT_CENTS = BigInt(100_000_000); // $1,000,000 guard
