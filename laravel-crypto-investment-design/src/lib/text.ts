/**
 * Pure text helpers. Kept free of database imports so client components can
 * use them without pulling the server runtime into the browser bundle.
 */

/** Split a headline into plain and *accented* parts. */
export function splitAccents(text: string): Array<{ text: string; accent: boolean }> {
  return text
    .split(/(\*[^*]+\*)/g)
    .filter((part) => part.length > 0)
    .map((part) =>
      part.startsWith("*") && part.endsWith("*") && part.length > 2
        ? { text: part.slice(1, -1), accent: true }
        : { text: part, accent: false },
    );
}
