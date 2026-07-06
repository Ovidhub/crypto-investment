import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import AuthForm from "@/components/AuthForm";

export const dynamic = "force-dynamic";

export default async function LoginPage() {
  const me = await getCurrentUser();
  if (me) redirect(me.role === "admin" ? "/admin" : "/dashboard");
  return <AuthForm mode="login" />;
}
