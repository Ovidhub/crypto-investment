import { redirect } from "next/navigation";
import { isDemoMode } from "@/db";
import { getCurrentUser } from "@/lib/auth";
import { DEMO_ACCOUNT } from "@/lib/demo-access";
import AuthForm from "@/components/AuthForm";

export const dynamic = "force-dynamic";

export default async function LoginPage() {
  const me = await getCurrentUser();
  if (me) redirect(me.role === "admin" ? "/admin" : "/dashboard");

  // The shared demo sign-in is only advertised when the app is running on the
  // in-memory demo database.
  const demo = isDemoMode
    ? { email: DEMO_ACCOUNT.email, password: DEMO_ACCOUNT.password }
    : undefined;

  return <AuthForm mode="login" demo={demo} />;
}
