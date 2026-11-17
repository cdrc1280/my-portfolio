import { Fragment, useEffect, useRef, useState } from 'react';
import type { CSSProperties, ReactNode } from 'react';
import { cn } from '@/lib/utils';

// The animations are CSS (`[data-reveal]`, `.reveal-word` in app.css), inside a
// `prefers-reduced-motion: no-preference` block, so they are static with reduced motion.

/** Splits on spaces and marks words inside *accent runs*, which may span several words. */
function splitAccents(text: string): Array<{ text: string; accent: boolean }> {
    const words: Array<{ text: string; accent: boolean }> = [];
    let open = false;

    for (const raw of text.split(' ')) {
        const opens = raw.startsWith('*');
        const closes = raw.replace(/[.,:;!?]$/, '').endsWith('*');
        words.push({ text: raw.replaceAll('*', ''), accent: open || opens });
        open = (open || opens) && !closes;
    }

    return words;
}

/** Fades and lifts its children into place the first time they scroll into view. Static with reduced motion. */
export function Reveal({
    children,
    className,
    delay = 0,
    y = 12,
}: {
    children: ReactNode;
    className?: string;
    delay?: number;
    y?: number;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const [shown, setShown] = useState(false);

    useEffect(() => {
        const element = ref.current;

        if (!element) {
            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry?.isIntersecting) {
                    setShown(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '0px 0px -8% 0px' },
        );
        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    return (
        <div
            ref={ref}
            data-reveal={shown ? 'shown' : 'hidden'}
            className={className}
            style={{ '--reveal-y': `${y}px`, '--reveal-delay': `${delay}s` } as CSSProperties}
        >
            {children}
        </div>
    );
}

/**
 * A headline whose words rise out of a mask one after another. Words wrapped in *asterisks* are set in
 * the accent colour, e.g. "I build *business systems* with Laravel". Screen readers get the plain text.
 */
export function RevealWords({
    text,
    className,
    as: Tag = 'h1',
    id,
}: {
    text: string;
    className?: string;
    as?: 'h1' | 'h2' | 'p';
    id?: string;
}) {
    const words = splitAccents(text);

    return (
        <Tag id={id} className={cn('text-balance', className)}>
            <span className="sr-only">{text.replaceAll('*', '')}</span>
            <span aria-hidden>
                {words.map((word, index) => (
                    // The space must sit outside the inline-block mask, or the browser collapses it.
                    <Fragment key={index}>
                        <span className="inline-block overflow-hidden pb-[0.1em] align-bottom">
                            <span
                                className={cn('reveal-word inline-block', word.accent && 'text-primary')}
                                style={{ '--word-delay': `${index * 0.045}s` } as CSSProperties}
                            >
                                {word.text}
                            </span>
                        </span>
                        {index < words.length - 1 && ' '}
                    </Fragment>
                ))}
            </span>
        </Tag>
    );
}
