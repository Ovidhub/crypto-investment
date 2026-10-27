import { randomBytes, scryptSync } from "node:crypto";

// Usage:
//   npm run env:generate
//   npm run env:generate -- "MyNewAdminPassword"
const password =
  process.argv[2] ?? `Enethi-Admin-${randomBytes(18).toString("base64url")}`;
const salt = randomBytes(16).toString("hex");
const derived = scryptSync(password, salt, 64).toString("hex");

console.log("Copy these into Vercel project environment variables:\n");
console.log(`AUTH_SECRET=${randomBytes(32).toString("hex")}`);
console.log(`ADMIN_EMAIL=admin@enethi.app`);
console.log(`ADMIN_PASSWORD_HASH=${salt}:${derived}`);
console.log(`\nAdmin password (save it now, it cannot be recovered): ${password}`);
