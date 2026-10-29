"use client";

import { useState } from "react";

const FAQS = [
  {
    q: "How is my balance held?",
    a: "Every balance is derived from a double-entry ledger: a credit to your wallet always has a matching debit somewhere else, and the trial balance is checked on the admin screen. No card or bank details are collected inside the app.",
  },
  {
    q: "How do the plan returns work?",
    a: "Each plan has a target annual rate. A scheduled job credits principal × rate ÷ 365 once a day while the plan runs and books the matching expense. It is arithmetic on a rate we publish, not a forecast of market performance, and it is not guaranteed.",
  },
  {
    q: "What fees do you charge?",
    a: "A 0.50% execution fee on buys and sells, recorded as revenue in the ledger. Deposits and withdrawals are reviewed manually and carry no fee.",
  },
  {
    q: "Which assets are supported?",
    a: "The 12 largest coins by market cap, priced live from CoinGecko. If that API is unreachable the app falls back to clearly-labelled sample prices so the interface still works.",
  },
  {
    q: "What happens to the data I enter?",
    a: "Your profile and activity are stored in the platform database. Verification records only the last four digits of a document number — never a full number or an image.",
  },
  {
    q: "Can I export or delete my data?",
    a: "You can download a JSON export or a CSV statement of your profile and balances from Settings. Account deletion is handled by support so the audit trail stays intact.",
  },
];

export default function Faq() {
  const [open, setOpen] = useState<number | null>(0);
  return (
    <section id="faq" className="mx-auto max-w-3xl px-5 py-20 sm:px-8">
      <div className="mb-10 text-center">
        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand">
          FAQ
        </p>
        <h2 className="mt-3 font-display text-[clamp(2rem,4vw,3rem)] font-bold">
          Frequently asked questions
        </h2>
      </div>

      <div className="space-y-3">
        {FAQS.map((f, i) => {
          const active = open === i;
          return (
            <div
              key={f.q}
              className="overflow-hidden rounded-2xl border border-line bg-card"
            >
              <button
                onClick={() => setOpen(active ? null : i)}
                className="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
              >
                <span className="font-semibold text-white">{f.q}</span>
                <span
                  className={`grid h-7 w-7 shrink-0 place-items-center rounded-full border border-line text-lg transition-transform ${
                    active ? "rotate-45 bg-brand text-white" : "text-[#9aa3b2]"
                  }`}
                >
                  +
                </span>
              </button>
              <div
                className={`grid transition-all duration-300 ${
                  active ? "grid-rows-[1fr] opacity-100" : "grid-rows-[0fr] opacity-0"
                }`}
              >
                <div className="overflow-hidden">
                  <p className="px-6 pb-5 text-sm leading-relaxed text-[#9aa3b2]">
                    {f.a}
                  </p>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
}
