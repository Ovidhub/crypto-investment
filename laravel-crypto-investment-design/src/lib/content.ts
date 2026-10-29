import { cache } from "react";
import { eq } from "drizzle-orm";
import { db } from "@/db";
import { siteContent } from "@/db/schema";

/**
 * Website content registry.
 *
 * Every string and image the marketing pages render lives here as a named
 * slot with a default. The super admin edits the values; the defaults are
 * what a fresh database shows, so the site is never empty and nothing breaks
 * if the table is missing.
 */

export type FieldType = "text" | "textarea" | "image";

export type ContentField = {
  key: string;
  label: string;
  type: FieldType;
  value: string;
  help?: string;
  maxLength?: number;
};

export type ContentGroup = {
  id: string;
  title: string;
  description: string;
  fields: ContentField[];
};

export const CONTENT_GROUPS: ContentGroup[] = [
  {
    id: "brand",
    title: "Brand & identity",
    description:
      "The name, logo and contact address used across the site, emails and page titles.",
    fields: [
      { key: "brand.name", label: "Site name", type: "text", value: "Actuatrades", maxLength: 40 },
      {
        key: "brand.tagline",
        label: "Page title",
        type: "text",
        value: "Invest In Crypto, Earn Passive Income Easily",
        maxLength: 120,
      },
      {
        key: "brand.description",
        label: "Meta description",
        type: "textarea",
        value:
          "Actuatrades is a crypto investment platform: live markets, managed plans and a double-entry ledger behind every balance.",
        maxLength: 300,
      },
      {
        key: "brand.logo",
        label: "Logo image",
        type: "image",
        value: "",
        help: "Leave empty to use the built-in letter mark.",
      },
      {
        key: "brand.supportEmail",
        label: "Support email",
        type: "text",
        value: "support@actuatrades.com",
        maxLength: 120,
      },
    ],
  },
  {
    id: "hero",
    title: "Home — hero",
    description:
      "The first screen visitors see. Wrap a word in *asterisks* to render it in the brand gradient.",
    fields: [
      {
        key: "home.hero.eyebrow",
        label: "Small pill text",
        type: "text",
        value: "Crypto investing platform of the future",
        maxLength: 80,
      },
      {
        key: "home.hero.title",
        label: "Headline",
        type: "text",
        value: "Invest In *Crypto* Earn Easy Passive *Money* Easily",
        maxLength: 140,
      },
      {
        key: "home.hero.subtitle",
        label: "Sub-headline",
        type: "textarea",
        value:
          "Actuatrades makes it simple to buy, sell and grow 99+ digital assets. Bank-grade security, real-time market data and a portfolio that works for you around the clock.",
        maxLength: 400,
      },
      { key: "home.hero.primaryLabel", label: "Primary button", type: "text", value: "Start Investing" },
      { key: "home.hero.primaryHref", label: "Primary button link", type: "text", value: "#join" },
      { key: "home.hero.secondaryLabel", label: "Secondary button", type: "text", value: "How It Works?" },
      { key: "home.hero.secondaryHref", label: "Secondary button link", type: "text", value: "#how" },
      {
        key: "home.hero.image",
        label: "Hero image",
        type: "image",
        value: "/images/hero-visual.webp",
        help: "Square PNG or WEBP works best.",
      },
      {
        key: "home.hero.investorsLabel",
        label: "Caption under the investor count",
        type: "text",
        value: "investors already onboard",
      },
    ],
  },
  {
    id: "marquee",
    title: "Home — market strip",
    description:
      "The scrolling strip under the hero. List the coins or venues you want to name, separated by commas.",
    fields: [
      {
        key: "home.marquee.label",
        label: "Caption",
        type: "text",
        value: "Markets covered",
        maxLength: 80,
      },
      {
        key: "home.marquee.items",
        label: "Items (comma separated)",
        type: "textarea",
        value: "Bitcoin, Ethereum, Solana, BNB, XRP, Cardano, Dogecoin, Avalanche",
        help: "Leave blank to hide the strip entirely.",
        maxLength: 400,
      },
    ],
  },
  {
    id: "features",
    title: "Home — feature cards",
    description: "The three cards directly under the hero.",
    fields: [
      { key: "home.features.eyebrow", label: "Eyebrow", type: "text", value: "What is actually built" },
      {
        key: "home.features.title",
        label: "Section title",
        type: "text",
        value: "A full product surface, built on a real ledger",
      },
      {
        key: "home.features.intro",
        label: "Intro paragraph",
        type: "textarea",
        value:
          "Actuatrades is a complete crypto investment platform: accounts, wallets, a double-entry ledger, approval queues, plans, referrals, notifications and an admin console, all backed by one source of truth for money.",
      },
      { key: "home.features.item1.title", label: "Card 1 title", type: "text", value: "Trade on live prices" },
      {
        key: "home.features.item1.text",
        label: "Card 1 text",
        type: "textarea",
        value:
          "Buy and sell the top coins at live market prices with integer-cent settlement. A 0.50% execution fee is booked to the platform ledger on every fill.",
      },
      { key: "home.features.item2.title", label: "Card 2 title", type: "text", value: "Managed plans" },
      {
        key: "home.features.item2.text",
        label: "Card 2 text",
        type: "textarea",
        value:
          "Lock capital in a plan and watch a daily accrual post. Every credit is arithmetic on a rate you can inspect, never a promised return.",
      },
      { key: "home.features.item3.title", label: "Card 3 title", type: "text", value: "Books you can audit" },
      {
        key: "home.features.item3.text",
        label: "Card 3 text",
        type: "textarea",
        value:
          "Every balance traces to a balanced journal entry. The admin ledger screen shows debits, credits and the difference in real time.",
      },
    ],
  },
  {
    id: "insights",
    title: "Home — product insight",
    description: "The image and copy block that explains how the platform works.",
    fields: [
      { key: "home.insights.image", label: "Image", type: "image", value: "/images/app-mockup.webp" },
      { key: "home.insights.eyebrow", label: "Eyebrow", type: "text", value: "Built like the real thing" },
      {
        key: "home.insights.title",
        label: "Title",
        type: "text",
        value: "The plumbing that makes a platform trustworthy",
      },
    ],
  },
  {
    id: "steps",
    title: "Home — how it works",
    description: "The three numbered steps.",
    fields: [
      { key: "home.steps.eyebrow", label: "Eyebrow", type: "text", value: "Simple process" },
      { key: "home.steps.title", label: "Section title", type: "text", value: "Start earning in 3 easy steps" },
      { key: "home.steps.item1.title", label: "Step 1 title", type: "text", value: "Create your account" },
      {
        key: "home.steps.item1.text",
        label: "Step 1 text",
        type: "textarea",
        value: "Sign up in under two minutes, then complete your profile so payouts reach the right person.",
      },
      { key: "home.steps.item2.title", label: "Step 2 title", type: "text", value: "Fund your wallet" },
      {
        key: "home.steps.item2.text",
        label: "Step 2 text",
        type: "textarea",
        value: "Request a deposit and our team reviews it before the balance lands in your wallet.",
      },
      { key: "home.steps.item3.title", label: "Step 3 title", type: "text", value: "Start investing" },
      {
        key: "home.steps.item3.text",
        label: "Step 3 text",
        type: "textarea",
        value: "Buy coins, join a plan or take a position on the trade desk, then track every credit on your statement.",
      },
    ],
  },
  {
    id: "why",
    title: "Home — why choose us",
    description: "The reasons grid beside the live market panel.",
    fields: [
      { key: "home.why.eyebrow", label: "Eyebrow", type: "text", value: "Why choose Actuatrades" },
      {
        key: "home.why.title",
        label: "Title",
        type: "text",
        value: "Everything you need in one investing account",
      },
      {
        key: "home.why.intro",
        label: "Intro paragraph",
        type: "textarea",
        value:
          "One application with multiple options to give you total freedom of buying and selling, built for beginners and professionals alike.",
      },
      { key: "home.why.reason1", label: "Reason 1", type: "text", value: "Ledger-backed balances" },
      { key: "home.why.reason2", label: "Reason 2", type: "text", value: "Live CoinGecko pricing" },
      { key: "home.why.reason3", label: "Reason 3", type: "text", value: "Human-reviewed payouts" },
      { key: "home.why.reason4", label: "Reason 4", type: "text", value: "Plans with transparent rates" },
    ],
  },
  {
    id: "cta",
    title: "Home — closing call to action",
    description: "The join block at the bottom of the home page.",
    fields: [
      { key: "home.cta.title", label: "Title", type: "text", value: "Join *Actuatrades* now" },
      {
        key: "home.cta.subtitle",
        label: "Subtitle",
        type: "textarea",
        value:
          "Create an account in minutes, complete your profile and fund your wallet. Our team reviews every deposit before it posts.",
      },
      { key: "home.cta.button", label: "Button label", type: "text", value: "Request early access" },
      {
        key: "home.cta.success",
        label: "Message after joining",
        type: "text",
        value: "You're on the list. We'll email you next steps.",
      },
    ],
  },
  {
    id: "about",
    title: "About page",
    description: "The story shown on /about.",
    fields: [
      { key: "about.title", label: "Headline", type: "text", value: "Built like a bank, priced like software" },
      {
        key: "about.intro",
        label: "Intro",
        type: "textarea",
        value:
          "Actuatrades started as a question: what would a crypto investment platform look like if every balance had to prove itself against a ledger? This is our answer.",
      },
      {
        key: "about.mission",
        label: "Mission paragraph",
        type: "textarea",
        value:
          "We publish target rates instead of promising returns, review every deposit by hand and let operators explain any adjustment they make to an account.",
      },
    ],
  },
  {
    id: "pricing",
    title: "Pricing page",
    description: "Headline copy above the plan catalogue.",
    fields: [
      { key: "pricing.eyebrow", label: "Eyebrow", type: "text", value: "Our products" },
      { key: "pricing.title", label: "Headline", type: "text", value: "Plans, with the maths shown" },
      {
        key: "pricing.intro",
        label: "Intro",
        type: "textarea",
        value:
          "No subscription is charged and no card is collected. These are the products in our catalogue, read live from the database.",
      },
    ],
  },
  {
    id: "contact",
    title: "Contact page",
    description: "How customers reach you.",
    fields: [
      { key: "contact.email", label: "Email", type: "text", value: "support@actuatrades.com" },
      { key: "contact.chat", label: "Chat line", type: "text", value: "Available 24/7 in-app" },
      { key: "contact.office", label: "Office line", type: "text", value: "Port Harcourt · Lagos · London" },
      {
        key: "contact.intro",
        label: "Intro",
        type: "textarea",
        value: "Questions about investing, security or partnerships? Our team is here to help around the clock.",
      },
    ],
  },
  {
    id: "footer",
    title: "Footer",
    description: "The block under every public page.",
    fields: [
      {
        key: "footer.about",
        label: "About text",
        type: "textarea",
        value: "The simplest way to invest in crypto and earn passive income. Secure, fast and built for everyone.",
      },
      { key: "footer.copyright", label: "Copyright line", type: "text", value: "© 2026 Actuatrades. All rights reserved." },
    ],
  },
];

/** Flat default map built from the registry. */
export function defaultContent(): Record<string, string> {
  const map: Record<string, string> = {};
  for (const group of CONTENT_GROUPS) {
    for (const field of group.fields) map[field.key] = field.value;
  }
  return map;
}

export type SiteContent = Record<string, string>;

const KNOWN_KEYS = new Set(Object.keys(defaultContent()));

/**
 * Read the saved content once per request. Falls back to the defaults when
 * the table does not exist yet or the query fails.
 */
export const getSiteContent = cache(async (): Promise<SiteContent> => {
  const content = defaultContent();
  try {
    const rows = await db
      .select({ key: siteContent.key, value: siteContent.value })
      .from(siteContent);
    for (const row of rows) {
      if (KNOWN_KEYS.has(row.key) && typeof row.value === "string") {
        content[row.key] = row.value;
      }
    }
  } catch {
    // A missing table (fresh database before bootstrap) just means defaults.
  }
  return content;
});

/** Value with a guaranteed string, falling back to the registry default. */
export function copy(content: SiteContent, key: string): string {
  const value = content[key];
  if (value !== undefined && value !== "") return value;
  return defaultContent()[key] ?? "";
}

export type SaveResult =
  | { ok: true; saved: number }
  | { ok: false; error: string; fields?: string[] };

/** Validate + persist a batch of edits. Unknown keys are rejected. */
export async function saveSiteContent(
  input: Record<string, unknown>,
  actorId: number | null,
): Promise<SaveResult> {
  const fields = CONTENT_GROUPS.flatMap((g) => g.fields);
  const byKey = new Map(fields.map((f) => [f.key, f]));

  const updates: Array<{ key: string; value: string }> = [];
  const invalid: string[] = [];

  for (const [key, raw] of Object.entries(input)) {
    const field = byKey.get(key);
    if (!field) continue;
    if (typeof raw !== "string") {
      invalid.push(key);
      continue;
    }
    const value = raw.trim().slice(0, field.maxLength ?? 2000);
    if (value === "") {
      // Empty means "reset to the default" — store nothing for this key.
      await db.delete(siteContent).where(eq(siteContent.key, key)).catch(() => undefined);
      continue;
    }
    updates.push({ key, value });
  }

  if (invalid.length > 0) {
    return {
      ok: false,
      error: "Some fields were not text values.",
      fields: invalid,
    };
  }

  for (const { key, value } of updates) {
    const [existing] = await db
      .select({ key: siteContent.key })
      .from(siteContent)
      .where(eq(siteContent.key, key))
      .limit(1);
    if (existing) {
      await db
        .update(siteContent)
        .set({ value, updatedAt: new Date(), updatedBy: actorId })
        .where(eq(siteContent.key, key));
    } else {
      await db.insert(siteContent).values({ key, value, updatedBy: actorId });
    }
  }

  return { ok: true, saved: updates.length };
}

export { splitAccents } from "@/lib/text";
