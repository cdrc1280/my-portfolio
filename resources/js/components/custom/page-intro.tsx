import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import type { ReactNode } from 'react';
import { Reveal, RevealWords } from '@/components/custom/reveal';
import { containers, measure } from '@/components/custom/section';
import { SectionLabel } from '@/components/custom/section-label';
import { cn } from '@/lib/utils';

type PageIntroProps = {
    label: string;
    /** Supports *accent* words (see RevealWords). */
    title: string;
    description?: ReactNode;
    back?: { label: string; href: NonNullable<InertiaLinkProps['href']> };
    width?: keyof typeof containers;
    children?: ReactNode;
};

/** The top of an inner page: optional back link, comment label, h1 with the word-rise animation, lead text. */
export function PageIntro({ label, title, description, back, width = 'page', children }: PageIntroProps) {
    return (
        <header className={cn(containers[width], 'pt-16 pb-12 sm:pt-20')}>
            {back && (
                <Link
                    href={back.href}
                    className="group mb-8 inline-flex items-center gap-1.5 rounded-md font-mono text-xs text-muted-foreground transition-colors duration-150 hover:text-foreground"
                >
                    <ArrowLeft
                        className="size-3.5 transition-transform duration-150 group-hover:-translate-x-0.5"
                        aria-hidden
                    />
                    {back.label}
                </Link>
            )}
            <div className="space-y-4">
                <Reveal y={6}>
                    <SectionLabel>{label}</SectionLabel>
                </Reveal>
                <RevealWords
                    text={title}
                    className="max-w-4xl text-4xl font-semibold tracking-tight text-foreground md:text-5xl"
                />
                {description && (
                    <Reveal delay={0.15}>
                        <p className="max-w-2xl text-lg leading-relaxed">{description}</p>
                    </Reveal>
                )}
            </div>
            {children && (
                <Reveal delay={0.25} className={cn('mt-8', measure)}>
                    {children}
                </Reveal>
            )}
        </header>
    );
}
