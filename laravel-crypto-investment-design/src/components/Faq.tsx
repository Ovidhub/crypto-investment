"use client";

import { useState } from "react";

const FAQS = [
  {
    q: "Is Enethi safe to use?",
    a: "Absolutely. We use cold-storage custody, mandatory 2FA, biometric login and carry full insurance on custodial assets. Your funds and data are always protected.",
  },
  {
    q: "How do I earn passive income?",
    a: "You can stake supported assets or deposit into our yield vaults to earn up to 12% APY. Rewards compound automatically and can be withdrawn anytime.",
  },
  {
    q: "What are the trading fees?",
    a: "Enethi charges a flat 0.1% maker/taker fee with no hidden charges. Deposits via crypto are always free, and card deposits carry a small processor fee.",
  },
  {
    q: "Which cryptocurrencies are supported?",
    a: "We support 99+ assets including Bitcoin, Ethereum, Solana, and all major DeFi and Layer-1 tokens. New coins are added every month.",
  },
  {
    q: "Can I use Enethi in my country?",
    a: "Enethi is available in 160+ countries. During sign-up we automatically check availability and applicable compliance requirements for your region.",
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
