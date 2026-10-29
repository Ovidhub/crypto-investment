import { eq } from "drizzle-orm";
import { db } from "@/db";
import { users } from "@/db/schema";
import { logAudit } from "@/lib/audit";

/**
 * Customer profile onboarding.
 *
 * A new account can sign in immediately but cannot use the product until the
 * profile below is complete — the dashboard layout redirects to `/onboarding`
 * until `profileCompletedAt` is set. The data is used for account
 * administration, statement headers and the verification workflow.
 */

export const TRADING_EXPERIENCE = [
  { key: "none", label: "No experience yet" },
  { key: "beginner", label: "Beginner — under a year" },
  { key: "intermediate", label: "Intermediate — 1 to 3 years" },
  { key: "experienced", label: "Experienced — 3 years or more" },
] as const;

export const INVESTMENT_GOALS = [
  { key: "grow_savings", label: "Grow my savings" },
  { key: "retirement", label: "Save for retirement" },
  { key: "short_term", label: "A shorter-term goal" },
  { key: "learn", label: "Learn how crypto works" },
  { key: "other", label: "Something else" },
] as const;

export const COUNTRIES = [
  "Nigeria",
  "Ghana",
  "Kenya",
  "South Africa",
  "Egypt",
  "United Kingdom",
  "Ireland",
  "United States",
  "Canada",
  "Germany",
  "France",
  "Netherlands",
  "Spain",
  "Portugal",
  "Italy",
  "Switzerland",
  "Sweden",
  "Norway",
  "Denmark",
  "Poland",
  "Turkey",
  "United Arab Emirates",
  "Saudi Arabia",
  "India",
  "Pakistan",
  "Bangladesh",
  "Indonesia",
  "Malaysia",
  "Singapore",
  "Philippines",
  "Vietnam",
  "Thailand",
  "Japan",
  "South Korea",
  "China",
  "Hong Kong",
  "Australia",
  "New Zealand",
  "Brazil",
  "Argentina",
  "Mexico",
  "Chile",
  "Colombia",
  "Peru",
  "Other",
] as const;

export type ProfileInput = {
  phone: string;
  country: string;
  dateOfBirth: string;
  addressLine: string;
  city: string;
  postalCode: string;
  occupation: string;
  tradingExperience: string;
  investmentGoal: string;
};

export type ProfileRecord = ProfileInput & {
  completedAt: string | null;
  complete: boolean;
};

export const EMPTY_PROFILE: ProfileInput = {
  phone: "",
  country: "",
  dateOfBirth: "",
  addressLine: "",
  city: "",
  postalCode: "",
  occupation: "",
  tradingExperience: "",
  investmentGoal: "",
};

/** Fields the customer must fill in. Postal code and occupation are optional. */
export const REQUIRED_FIELDS: (keyof ProfileInput)[] = [
  "phone",
  "country",
  "dateOfBirth",
  "addressLine",
  "city",
  "tradingExperience",
  "investmentGoal",
];

export function isExperience(value: string): boolean {
  return TRADING_EXPERIENCE.some((option) => option.key === value);
}

export function isGoal(value: string): boolean {
  return INVESTMENT_GOALS.some((option) => option.key === value);
}

/** Whole years between a YYYY-MM-DD date and today. */
export function ageFrom(date: string): number | null {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) return null;
  const born = new Date(`${date}T00:00:00Z`);
  if (Number.isNaN(born.getTime())) return null;
  const now = new Date();
  let age = now.getUTCFullYear() - born.getUTCFullYear();
  const month = now.getUTCMonth() - born.getUTCMonth();
  if (month < 0 || (month === 0 && now.getUTCDate() < born.getUTCDate())) age -= 1;
  return age;
}

export type ProfileValidation =
  | { ok: true; value: ProfileInput }
  | { ok: false; errors: Partial<Record<keyof ProfileInput, string>> };

export function validateProfile(raw: Partial<Record<keyof ProfileInput, unknown>>): ProfileValidation {
  const value: ProfileInput = {
    phone: String(raw.phone ?? "").trim(),
    country: String(raw.country ?? "").trim(),
    dateOfBirth: String(raw.dateOfBirth ?? "").trim(),
    addressLine: String(raw.addressLine ?? "").trim(),
    city: String(raw.city ?? "").trim(),
    postalCode: String(raw.postalCode ?? "").trim(),
    occupation: String(raw.occupation ?? "").trim(),
    tradingExperience: String(raw.tradingExperience ?? "").trim(),
    investmentGoal: String(raw.investmentGoal ?? "").trim(),
  };

  const errors: Partial<Record<keyof ProfileInput, string>> = {};

  // Digits, spaces and the usual punctuation; 7–20 digits after cleaning.
  const digits = value.phone.replace(/[^\d]/g, "");
  if (!value.phone) errors.phone = "Add a phone number we can reach you on.";
  else if (digits.length < 7 || digits.length > 20) {
    errors.phone = "That phone number does not look right.";
  }

  if (!value.country) errors.country = "Choose your country.";
  else if (!(COUNTRIES as readonly string[]).includes(value.country)) {
    errors.country = "Choose your country from the list.";
  }

  const age = ageFrom(value.dateOfBirth);
  if (!value.dateOfBirth) errors.dateOfBirth = "Add your date of birth.";
  else if (age === null) errors.dateOfBirth = "Use the date picker to set your date of birth.";
  else if (age < 18) errors.dateOfBirth = "You must be at least 18 to hold an account.";
  else if (age > 120) errors.dateOfBirth = "Check the year — that date is not valid.";

  if (value.addressLine.length < 4) errors.addressLine = "Add your street address.";
  if (value.addressLine.length > 200) errors.addressLine = "Keep the address under 200 characters.";

  if (value.city.length < 2) errors.city = "Add your city.";
  if (value.postalCode.length > 20) errors.postalCode = "Keep the postal code under 20 characters.";
  if (value.occupation.length > 120) errors.occupation = "Keep the occupation under 120 characters.";

  if (!isExperience(value.tradingExperience)) {
    errors.tradingExperience = "Tell us how much trading experience you have.";
  }
  if (!isGoal(value.investmentGoal)) {
    errors.investmentGoal = "Tell us what you are investing for.";
  }

  if (Object.keys(errors).length > 0) return { ok: false, errors };
  return { ok: true, value };
}

export async function getProfile(userId: number): Promise<ProfileRecord> {
  const [row] = await db
    .select({
      phone: users.phone,
      country: users.country,
      dateOfBirth: users.dateOfBirth,
      addressLine: users.addressLine,
      city: users.city,
      postalCode: users.postalCode,
      occupation: users.occupation,
      tradingExperience: users.tradingExperience,
      investmentGoal: users.investmentGoal,
      profileCompletedAt: users.profileCompletedAt,
    })
    .from(users)
    .where(eq(users.id, userId))
    .limit(1);

  const completedAt = row?.profileCompletedAt ? new Date(row.profileCompletedAt).toISOString() : null;
  return {
    phone: row?.phone ?? "",
    country: row?.country ?? "",
    dateOfBirth: row?.dateOfBirth ?? "",
    addressLine: row?.addressLine ?? "",
    city: row?.city ?? "",
    postalCode: row?.postalCode ?? "",
    occupation: row?.occupation ?? "",
    tradingExperience: row?.tradingExperience ?? "",
    investmentGoal: row?.investmentGoal ?? "",
    completedAt,
    complete: completedAt !== null,
  };
}

export async function isProfileComplete(userId: number): Promise<boolean> {
  const profile = await getProfile(userId);
  return profile.complete;
}

export type SaveProfileResult =
  | { ok: true; message: string }
  | { ok: false; errors: Partial<Record<keyof ProfileInput, string>> };

export async function saveProfile(input: {
  userId: number;
  actorEmail: string;
  raw: Partial<Record<keyof ProfileInput, unknown>>;
  /** True when the customer finishes onboarding for the first time. */
  firstTime?: boolean;
}): Promise<SaveProfileResult> {
  const validated = validateProfile(input.raw);
  if (!validated.ok) return validated;

  const profile = validated.value;
  await db
    .update(users)
    .set({
      phone: profile.phone,
      country: profile.country,
      dateOfBirth: profile.dateOfBirth,
      addressLine: profile.addressLine,
      city: profile.city,
      postalCode: profile.postalCode || null,
      occupation: profile.occupation || null,
      tradingExperience: profile.tradingExperience,
      investmentGoal: profile.investmentGoal,
      profileCompletedAt: new Date(),
    })
    .where(eq(users.id, input.userId));

  await logAudit({
    actorId: input.userId,
    actorEmail: input.actorEmail,
    action: input.firstTime ? "profile.completed" : "profile.updated",
    targetType: "user",
    targetId: input.userId,
  });

  return {
    ok: true,
    message: input.firstTime ? "Profile complete. Welcome aboard." : "Profile updated.",
  };
}
