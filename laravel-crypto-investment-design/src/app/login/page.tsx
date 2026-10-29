import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import AuthForm from "@/components/AuthForm";

export const dynamic = "force-dynamic";

export default async function LoginPage() {
  const me = await getCurrentUser();
  if (me && (me.role === "admin" || me.role === "owner")) redirect("/admin");
  if (me) redirect(me.profileCompletedAt ? "/dashboard" : "/onboarding");
  return <AuthForm mode="login" />;
}
