import { motion, useReducedMotion } from 'motion/react';
import { Fragment } from 'react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

const EASE = [0.22, 1, 0.36, 1] as const;

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
    const reduceMotion = useReducedMotion();

    return (
        <motion.div
            className={className}
            initial={reduceMotion ? false : { opacity: 0, y }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '0px 0px -8% 0px' }}
            transition={{ duration: 0.6, ease: EASE, delay }}
        >
            {children}
        </motion.div>
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
    const reduceMotion = useReducedMotion();
    const words = splitAccents(text);

    return (
        <Tag id={id} className={cn('text-balance', className)}>
            <span className="sr-only">{text.replaceAll('*', '')}</span>
            <motion.span
                aria-hidden
                initial={reduceMotion ? false : 'hidden'}
                animate="shown"
                transition={{ staggerChildren: 0.045 }}
            >
                {words.map((word, index) => (
                    // The space must sit outside the inline-block mask, or the browser collapses it.
                    <Fragment key={index}>
                        <span className="inline-block overflow-hidden pb-[0.1em] align-bottom">
                            <motion.span
                                className={cn('inline-block', word.accent && 'text-primary')}
                                variants={{
                                    hidden: { y: '105%' },
                                    shown: { y: '0%', transition: { duration: 0.6, ease: EASE } },
                                }}
                            >
                                {word.text}
                            </motion.span>
                        </span>
                        {index < words.length - 1 && ' '}
                    </Fragment>
                ))}
            </motion.span>
        </Tag>
    );
}
