import Link from "next/link";
import type { ReactNode } from "react";

export default function EmptyState({
  title,
  body,
  action,
  href,
  children,
}: {
  title: string;
  body: string;
  action?: string;
  href?: string;
  children?: ReactNode;
}) {
  return (
    <div className="p-10 text-center">
      <p className="font-display font-bold text-white">{title}</p>
      <p className="mx-auto mt-2 max-w-sm text-sm text-[#9aa3b2]">{body}</p>
      {children}
      {action && href && (
        <Link
          href={href}
          className="mt-5 inline-block rounded-xl bg-gradient-to-r from-brand to-brand-2 px-5 py-2.5 text-sm font-semibold text-white"
        >
          {action}
        </Link>
      )}
    </div>
  );
}
