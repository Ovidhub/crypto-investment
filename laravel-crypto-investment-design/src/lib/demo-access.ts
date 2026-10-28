/**
 * A ready-made demo account so anyone can look at the user side of the site
 * without registering. It is seeded in demo mode only, and it is a normal
 * user account: no admin rights, no suspension bypass.
 *
 * Override the credentials with DEMO_LOGIN_EMAIL / DEMO_LOGIN_PASSWORD if you
 * want a private sign-in on a public deployment.
 */
export const DEMO_ACCOUNT = {
  name: "Demo Account",
  email: (process.env.DEMO_LOGIN_EMAIL ?? "demo@enethi.app").trim().toLowerCase(),
  password: process.env.DEMO_LOGIN_PASSWORD ?? "Enethi-Demo-2026!42",
  role: "user",
} as const;
