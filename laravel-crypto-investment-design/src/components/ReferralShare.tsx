"use client";

import { useState } from "react";

export default function ReferralShare({ code, link }: { code: string; link: string }) {
  const [copied, setCopied] = useState(false);

  async function copy() {
    try {
      await navigator.clipboard.writeText(link);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch {
      setCopied(false);
    }
  }

  return (
    <div className="rounded-2xl border border-line bg-gradient-to-br from-brand/15 to-brand-2/5 p-6">
      <p className="text-xs text-[#9aa3b2]">Your referral code</p>
      <p className="mt-1 font-display text-2xl font-bold tracking-wider text-white">{code}</p>
      <div className="mt-4 flex flex-col gap-2 sm:flex-row">
        <input
          readOnly
          value={link}
          className="flex-1 rounded-xl border border-line bg-ink px-4 py-2.5 text-xs text-[#c7cedb] outline-none"
        />
        <button
          onClick={copy}
          className="rounded-xl bg-gradient-to-r from-brand to-brand-2 px-4 py-2.5 text-xs font-semibold text-white"
        >
          {copied ? "Copied" : "Copy link"}
        </button>
      </div>
      <p className="mt-3 text-[11px] text-[#7c8698]">
        Referral rewards are credited to your balance and recorded in the
        ledger, like every other movement on your account.
      </p>
    </div>
  );
}
