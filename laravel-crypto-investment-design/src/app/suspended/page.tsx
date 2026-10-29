import Link from "next/link";
import { clearSession } from "@/lib/auth";

export const dynamic = "force-dynamic";

export default function SuspendedPage() {
  async function signOut() {
    "use server";
    await clearSession();
  }

  return (
    <main className="grid min-h-screen place-items-center px-6">
      <div className="max-w-md rounded-2xl border border-line bg-card p-8 text-center">
        <p className="font-display text-2xl font-bold text-white">Account suspended</p>
        <p className="mt-3 text-sm text-[#9aa3b2]">
          This account has been suspended by an operator. If you believe that is a
          mistake, contact support with the email on the account.
        </p>
        <form action={signOut} className="mt-6">
          <button className="rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-[#c7cedb] hover:border-brand hover:text-white">
            Sign out
          </button>
        </form>
        <Link
          href="/contact"
          className="mt-4 block text-xs font-semibold text-brand hover:underline"
        >
          Contact support
        </Link>
      </div>
    </main>
  );
}
