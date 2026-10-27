"use client";

import { useState } from "react";

const FAQS = [
  {
    q: "Is this a real investment platform?",
    a: "No. Enethi is a product demonstration. Every balance, deposit, withdrawal and yield credit is simulated in a database. There is no payment processor, no custody of real assets and no blockchain transaction anywhere in the app.",
  },
  {
    q: "How do the plan returns work?",
    a: "Each plan has a simulated annual rate. A scheduled job credits principal × rate ÷ 365 once a day while the plan runs, and books the matching simulated-yield expense. It is arithmetic on our own input, not a forecast of market performance, and it is not guaranteed.",
  },
  {
    q: "What fees does it charge?",
    a: "A 0.50% simulated execution fee on buys and sells, recorded as revenue in the ledger. Deposits and withdrawals are reviewed manually and carry no fee in this demo.",
  },
  {
    q: "Which assets are supported?",
    a: "The 12 largest coins by market cap, priced live from CoinGecko. If that API is unreachable the app falls back to clearly-labelled sample prices so the interface still works.",
  },
  {
    q: "What happens to the data I enter?",
    a: "By default the demo runs on an in-process database that resets when the server restarts. If an operator sets DATABASE_URL it persists in Postgres. Do not enter real identity documents: the verification screen is a simulated workflow that stores only the last four digits.",
  },
  {
    q: "Can I export or delete my data?",
    a: "You can download a JSON export of your profile and ledger-backed balances from Settings. Account deletion is handled by support in this demo rather than a self-serve button, because the audit trail has to stay intact.",
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
