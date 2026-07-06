import { getWaitlist } from "@/lib/admin";

export const dynamic = "force-dynamic";

export default async function AdminWaitlistPage() {
  const rows = await getWaitlist(300);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="font-display text-2xl font-bold sm:text-3xl">Waitlist</h1>
        <p className="mt-1 text-sm text-[#9aa3b2]">
          {rows.length.toLocaleString()} people signed up for early access.
        </p>
      </div>

      <div className="overflow-hidden rounded-2xl border border-line bg-card">
        {rows.length === 0 ? (
          <p className="p-10 text-center text-sm text-[#9aa3b2]">
            No signups yet.
          </p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full min-w-[560px] text-left text-sm">
              <thead>
                <tr className="border-b border-line text-xs uppercase tracking-wider text-[#7c8698]">
                  <th className="px-6 py-4 font-medium">Name</th>
                  <th className="px-6 py-4 font-medium">Email</th>
                  <th className="px-6 py-4 font-medium">Plan</th>
                  <th className="px-6 py-4 text-right font-medium">Joined</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((r) => (
                  <tr key={r.id} className="border-b border-line/60 last:border-0">
                    <td className="px-6 py-4 font-semibold text-white">
                      {r.name}
                    </td>
                    <td className="px-6 py-4 text-[#c7cedb]">{r.email}</td>
                    <td className="px-6 py-4">
                      <span className="rounded-full bg-brand/15 px-2.5 py-1 text-xs font-semibold capitalize text-brand">
                        {r.plan}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right text-[#7c8698]">
                      {new Date(r.createdAt).toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                      })}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
