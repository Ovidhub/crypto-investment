import { splitAccents } from "@/lib/text";

/**
 * Renders CMS headline text. Anything wrapped in *asterisks* gets the brand
 * gradient, so an editor can highlight a word without touching code.
 */
export default function Accent({ text }: { text: string }) {
  return (
    <>
      {splitAccents(text).map((part, index) =>
        part.accent ? (
          <span key={index} className="brand-gradient">
            {part.text}
          </span>
        ) : (
          <span key={index}>{part.text}</span>
        ),
      )}
    </>
  );
}
