/**
 * A ready-made reviewer account so the product can be inspected without a
 * fresh registration. It is a normal customer account: no admin rights, no
 * suspension bypass, and it has a complete profile.
 *
 * Override with DEMO_LOGIN_EMAIL / DEMO_LOGIN_PASSWORD if you want private
 * credentials on a deployment.
 */
export const DEMO_ACCOUNT = {
  name: "Amara Obi",
  email: (process.env.DEMO_LOGIN_EMAIL ?? "amara@enethi.app").trim().toLowerCase(),
  password: process.env.DEMO_LOGIN_PASSWORD ?? "Enethi-Demo-2026!42",
  role: "user",
} as const;
