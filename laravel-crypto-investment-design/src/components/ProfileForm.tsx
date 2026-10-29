"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

type Field =
  | "phone"
  | "country"
  | "dateOfBirth"
  | "addressLine"
  | "city"
  | "postalCode"
  | "occupation"
  | "tradingExperience"
  | "investmentGoal";

export type ProfileValues = Record<Field, string>;

const EXPERIENCE = [
  { key: "none", label: "No experience yet" },
  { key: "beginner", label: "Beginner — under a year" },
  { key: "intermediate", label: "Intermediate — 1 to 3 years" },
  { key: "experienced", label: "Experienced — 3 years or more" },
];

const GOALS = [
  { key: "grow_savings", label: "Grow my savings" },
  { key: "retirement", label: "Save for retirement" },
  { key: "short_term", label: "A shorter-term goal" },
  { key: "learn", label: "Learn how crypto works" },
  { key: "other", label: "Something else" },
];

const COUNTRIES = [
  "Nigeria", "Ghana", "Kenya", "South Africa", "Egypt", "United Kingdom", "Ireland",
  "United States", "Canada", "Germany", "France", "Netherlands", "Spain", "Portugal",
  "Italy", "Switzerland", "Sweden", "Norway", "Denmark", "Poland", "Turkey",
  "United Arab Emirates", "Saudi Arabia", "India", "Pakistan", "Bangladesh",
  "Indonesia", "Malaysia", "Singapore", "Philippines", "Vietnam", "Thailand",
  "Japan", "South Korea", "China", "Hong Kong", "Australia", "New Zealand",
  "Brazil", "Argentina", "Mexico", "Chile", "Colombia", "Peru", "Other",
];

const STEPS = [
  { title: "Contact", body: "How we reach you" },
  { title: "Identity", body: "Who we are verifying" },
  { title: "Experience", body: "What you are here for" },
];

function inputClass(error?: string): string {
  return `w-full rounded-xl border bg-ink px-4 py-2.5 text-sm text-white outline-none placeholder:text-[#6b7488] focus:border-brand ${
    error ? "border-[#f6465d]" : "border-line"
  }`;
}

export default function ProfileForm({
  initial,
  firstName,
  editing,
}: {
  initial: ProfileValues;
  firstName: string;
  editing?: boolean;
}) {
  const router = useRouter();
  const [step, setStep] = useState(0);
  const [values, setValues] = useState<ProfileValues>(initial);
  const [errors, setErrors] = useState<Partial<Record<Field, string>>>({});
  const [message, setMessage] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  function set(field: Field, value: string) {
    setValues((current) => ({ ...current, [field]: value }));
    setErrors((current) => ({ ...current, [field]: undefined }));
  }

  const stepReady = (index: number): boolean => {
    const required: Field[] =
      index === 0
        ? ["phone", "country"]
        : index === 1
          ? ["dateOfBirth", "addressLine", "city"]
          : ["tradingExperience", "investmentGoal"];
    return required.every((field) => values[field].trim().length > 0);
  };

  const filled = (
    [
      "phone",
      "country",
      "dateOfBirth",
      "addressLine",
      "city",
      "tradingExperience",
      "investmentGoal",
    ] as Field[]
  ).filter((field) => values[field].trim().length > 0).length;
  const progress = Math.round((filled / 7) * 100);

  async function submit() {
    setBusy(true);
    setErrors({});
    setMessage(null);
    try {
      const res = await fetch("/api/profile", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify(values),
      });
      const data = (await res.json()) as {
        error?: string;
        errors?: Partial<Record<Field, string>>;
        message?: string;
      };
      if (!res.ok) {
        setErrors(data.errors ?? {});
        setMessage(data.error ?? "Could not save your profile.");
        if (data.errors?.phone || data.errors?.country) setStep(0);
        else if (data.errors?.dateOfBirth || data.errors?.addressLine || data.errors?.city) setStep(1);
        else setStep(2);
      } else {
        setMessage(data.message ?? "Saved.");
        router.push("/dashboard");
        router.refresh();
      }
    } catch {
      setMessage("Network error. Please try again.");
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="space-y-6">
      <div className="rounded-3xl border border-line bg-card p-6 sm:p-8">
        <div className="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h1 className="font-display text-2xl font-bold">
              {editing ? "Your profile" : `Finish setting up, ${firstName}`}
            </h1>
            <p className="mt-1 max-w-md text-sm text-[#9aa3b2]">
              {editing
                ? "Keep your details current — they appear on your statements and requests."
                : "Tell us a little about yourself so we can verify your account and keep your payouts and statements in the right name."}
            </p>
          </div>
          <div className="w-full max-w-[180px]">
            <div className="flex items-center justify-between text-[11px] text-[#9aa3b2]">
              <span>Profile</span>
              <span className="tabular-nums">{progress}%</span>
            </div>
            <div className="mt-1.5 h-1.5 overflow-hidden rounded-full bg-panel-2">
              <div
                className="h-full rounded-full bg-gradient-to-r from-brand to-brand-2 transition-all"
                style={{ width: `${progress}%` }}
              />
            </div>
          </div>
        </div>

        <ol className="mt-6 grid gap-2 sm:grid-cols-3">
          {STEPS.map((s, index) => {
            const active = index === step;
            const done = index < step && stepReady(index);
            return (
              <li key={s.title}>
                <button
                  type="button"
                  onClick={() => setStep(index)}
                  className={`w-full rounded-xl border px-4 py-3 text-left transition-colors ${
                    active
                      ? "border-brand/50 bg-brand/10"
                      : "border-line bg-panel-2 hover:border-brand/30"
                  }`}
                >
                  <span className="flex items-center gap-2 text-xs font-semibold text-white">
                    <span
                      className={`grid h-5 w-5 place-items-center rounded-full text-[10px] ${
                        done
                          ? "bg-accent text-ink"
                          : active
                            ? "bg-brand text-ink"
                            : "bg-white/10 text-[#9aa3b2]"
                      }`}
                    >
                      {done ? "✓" : index + 1}
                    </span>
                    {s.title}
                  </span>
                  <span className="mt-1 block text-[11px] text-[#7c8698]">{s.body}</span>
                </button>
              </li>
            );
          })}
        </ol>

        <div className="mt-6 space-y-4">
          {step === 0 && (
            <>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Phone number</span>
                <input
                  value={values.phone}
                  onChange={(e) => set("phone", e.target.value)}
                  inputMode="tel"
                  autoComplete="tel"
                  placeholder="+234 801 234 5678"
                  className={inputClass(errors.phone)}
                />
                {errors.phone && <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.phone}</span>}
              </label>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Country of residence</span>
                <select
                  value={values.country}
                  onChange={(e) => set("country", e.target.value)}
                  className={inputClass(errors.country)}
                >
                  <option value="">Select a country</option>
                  {COUNTRIES.map((country) => (
                    <option key={country} value={country}>
                      {country}
                    </option>
                  ))}
                </select>
                {errors.country && (
                  <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.country}</span>
                )}
              </label>
            </>
          )}

          {step === 1 && (
            <>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Date of birth</span>
                <input
                  type="date"
                  value={values.dateOfBirth}
                  onChange={(e) => set("dateOfBirth", e.target.value)}
                  max={new Date().toISOString().slice(0, 10)}
                  className={inputClass(errors.dateOfBirth)}
                />
                <span className="mt-1 block text-[11px] text-[#7c8698]">You must be 18 or older.</span>
                {errors.dateOfBirth && (
                  <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.dateOfBirth}</span>
                )}
              </label>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">Street address</span>
                <input
                  value={values.addressLine}
                  onChange={(e) => set("addressLine", e.target.value)}
                  autoComplete="street-address"
                  placeholder="12 Aba Road"
                  className={inputClass(errors.addressLine)}
                />
                {errors.addressLine && (
                  <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.addressLine}</span>
                )}
              </label>
              <div className="grid gap-4 sm:grid-cols-2">
                <label className="block">
                  <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">City</span>
                  <input
                    value={values.city}
                    onChange={(e) => set("city", e.target.value)}
                    autoComplete="address-level2"
                    placeholder="Port Harcourt"
                    className={inputClass(errors.city)}
                  />
                  {errors.city && (
                    <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.city}</span>
                  )}
                </label>
                <label className="block">
                  <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                    Postal code <span className="font-normal text-[#7c8698]">(optional)</span>
                  </span>
                  <input
                    value={values.postalCode}
                    onChange={(e) => set("postalCode", e.target.value)}
                    autoComplete="postal-code"
                    placeholder="500001"
                    className={inputClass(errors.postalCode)}
                  />
                </label>
              </div>
            </>
          )}

          {step === 2 && (
            <>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                  Occupation <span className="font-normal text-[#7c8698]">(optional)</span>
                </span>
                <input
                  value={values.occupation}
                  onChange={(e) => set("occupation", e.target.value)}
                  placeholder="e.g. Software engineer"
                  className={inputClass(errors.occupation)}
                />
              </label>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                  How much trading experience do you have?
                </span>
                <select
                  value={values.tradingExperience}
                  onChange={(e) => set("tradingExperience", e.target.value)}
                  className={inputClass(errors.tradingExperience)}
                >
                  <option value="">Select one</option>
                  {EXPERIENCE.map((option) => (
                    <option key={option.key} value={option.key}>
                      {option.label}
                    </option>
                  ))}
                </select>
                {errors.tradingExperience && (
                  <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.tradingExperience}</span>
                )}
              </label>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold text-[#c7cedb]">
                  What are you mainly using Actuatrades for?
                </span>
                <select
                  value={values.investmentGoal}
                  onChange={(e) => set("investmentGoal", e.target.value)}
                  className={inputClass(errors.investmentGoal)}
                >
                  <option value="">Select one</option>
                  {GOALS.map((option) => (
                    <option key={option.key} value={option.key}>
                      {option.label}
                    </option>
                  ))}
                </select>
                {errors.investmentGoal && (
                  <span className="mt-1 block text-[11px] text-[#f6465d]">{errors.investmentGoal}</span>
                )}
              </label>
            </>
          )}
        </div>

        {message && (
          <p className="mt-4 rounded-xl border border-[#f6465d]/30 bg-[#f6465d]/5 px-4 py-2.5 text-sm text-[#f6465d]">
            {message}
          </p>
        )}

        <div className="mt-6 flex flex-wrap items-center justify-between gap-3">
          <button
            type="button"
            onClick={() => setStep((s) => Math.max(0, s - 1))}
            disabled={step === 0}
            className="rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white disabled:opacity-40"
          >
            Back
          </button>

          {step < STEPS.length - 1 ? (
            <button
              type="button"
              onClick={() => setStep((s) => Math.min(STEPS.length - 1, s + 1))}
              disabled={!stepReady(step)}
              className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
            >
              Continue
            </button>
          ) : (
            <button
              type="button"
              onClick={submit}
              disabled={busy || !stepReady(0) || !stepReady(1) || !stepReady(2)}
              className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
            >
              {busy ? "Saving…" : editing ? "Save changes" : "Complete profile"}
            </button>
          )}
        </div>
      </div>

      <p className="text-center text-[11px] leading-relaxed text-[#7c8698]">
        Your details are used to administer your account and appear on your statements.
        We never share them with third parties for marketing.
      </p>
    </div>
  );
}
