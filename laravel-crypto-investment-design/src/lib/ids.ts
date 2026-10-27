import { randomBytes } from "node:crypto";

const ALPHABET = "23456789ABCDEFGHJKLMNPQRSTUVWXYZ";

/** Human-readable reference code for support tickets, deposits, withdrawals. */
export function reference(prefix: string, length = 8): string {
  const bytes = randomBytes(length);
  let out = "";
  for (let i = 0; i < length; i += 1) {
    out += ALPHABET[bytes[i] % ALPHABET.length];
  }
  return `${prefix}-${out}`;
}

/** Referral codes exclude look-alike characters so they can be read aloud. */
export function referralCode(length = 8): string {
  return reference("REF", length).replace("REF-", "");
}

export function last4(value: string): string {
  const digits = value.replace(/\D/g, "");
  return digits.slice(-4).padStart(4, "0");
}
