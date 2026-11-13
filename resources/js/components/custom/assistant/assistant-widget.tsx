import { Link, usePage } from '@inertiajs/react';
import { ArrowUpRight, Send, X } from 'lucide-react';
import type { FormEvent } from 'react';
import { useEffect, useId, useRef, useState } from 'react';
import AppLogoIcon from '@/components/custom/app-logo-icon';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { track } from '@/lib/analytics';
import { cn } from '@/lib/utils';
import { ask } from '@/routes/assistant';
import type { AssistantReply, ChatMessage } from '@/types/domains/assistant';

const STARTERS = [
    'What is your tech stack?',
    'Are you available for work?',
    'Show me your projects',
    'How can I contact you?',
];

/** Laravel's CSRF token, which it also sets as the (unencrypted-for-JS) XSRF-TOKEN cookie. */
function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

async function fetchReply(question: string): Promise<AssistantReply> {
    const response = await fetch(ask.url(), {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': xsrfToken(),
        },
        credentials: 'same-origin',
        body: JSON.stringify({ question }),
    });

    if (response.status === 429) {
        throw new Error('That is a lot of questions! Please wait a minute and try again.');
    }

    if (!response.ok) {
        throw new Error('Something went wrong. Please try again, or use the contact form.');
    }

    return (await response.json()) as AssistantReply;
}

/**
 * Floating "Ask about me" chat. Answers come from the site's own content (no AI service, no cost),
 * so they are always accurate. Turned on and off in Settings → Features.
 */
export function AssistantWidget() {
    const { site } = usePage().props;
    const [open, setOpen] = useState(false);
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [question, setQuestion] = useState('');
    const [pending, setPending] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const nextId = useRef(1);
    const scroller = useRef<HTMLDivElement>(null);
    const input = useRef<HTMLInputElement>(null);
    const panelId = useId();
    const firstName = site.name.split(' ')[0];

    useEffect(() => {
        scroller.current?.scrollTo({ top: scroller.current.scrollHeight, behavior: 'smooth' });
    }, [messages, pending]);

    useEffect(() => {
        if (!open) {
            return;
        }

        input.current?.focus();
        const onKey = (event: KeyboardEvent) => event.key === 'Escape' && setOpen(false);
        window.addEventListener('keydown', onKey);

        return () => window.removeEventListener('keydown', onKey);
    }, [open]);

    const send = async (text: string) => {
        const trimmed = text.trim();

        if (!trimmed || pending) {
            return;
        }

        setQuestion('');
        setError(null);
        setPending(true);
        setMessages((current) => [...current, { id: nextId.current++, from: 'visitor', text: trimmed }]);
        track('Assistant question');

        try {
            const reply = await fetchReply(trimmed);
            setMessages((current) => [...current, { id: nextId.current++, from: 'assistant', reply }]);
        } catch (caught) {
            setError(caught instanceof Error ? caught.message : 'Something went wrong.');
        } finally {
            setPending(false);
        }
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();
        void send(question);
    };

    const lastReply = [...messages].reverse().find((message) => message.from === 'assistant');
    const suggestions =
        lastReply?.from === 'assistant' ? lastReply.reply.suggestions : messages.length === 0 ? STARTERS : [];

    return (
        <div className="fixed right-4 bottom-4 z-[60] flex flex-col items-end gap-3 sm:right-6 sm:bottom-6">
            {open && (
                <section
                    id={panelId}
                    role="dialog"
                    aria-label={`Ask about ${firstName}`}
                    className="flex h-[min(34rem,calc(100svh-6rem))] w-[calc(100vw-2rem)] flex-col overflow-hidden rounded-2xl border border-border bg-background shadow-xl sm:w-96"
                >
                    <header className="flex items-center justify-between gap-3 border-b border-border px-4 py-3">
                        <div className="flex min-w-0 items-center gap-3">
                            <Avatar className="size-9 border border-border">
                                {site.avatar_url && <AvatarImage src={site.avatar_url} alt="" />}
                                <AvatarFallback className="bg-primary text-primary-foreground">
                                    <AppLogoIcon className="size-5" />
                                </AvatarFallback>
                            </Avatar>
                            <div className="min-w-0">
                                <h2 className="text-sm font-semibold text-foreground">Ask about {firstName}</h2>
                                <p className="text-xs text-muted-foreground">
                                    Automated · answers come only from this site.
                                </p>
                            </div>
                        </div>
                        <Button variant="ghost" size="icon" aria-label="Close chat" onClick={() => setOpen(false)}>
                            <X aria-hidden />
                        </Button>
                    </header>

                    <div
                        ref={scroller}
                        className="flex-1 space-y-3 overflow-y-auto px-4 py-4 text-sm"
                        aria-live="polite"
                    >
                        {messages.length === 0 && (
                            <p className="rounded-xl bg-muted px-3 py-2 text-foreground">
                                Hi! Ask me about {firstName}&apos;s skills, projects, experience or availability.
                            </p>
                        )}

                        {messages.map((message) =>
                            message.from === 'visitor' ? (
                                <p
                                    key={message.id}
                                    className="ml-auto w-fit max-w-[85%] rounded-xl bg-primary px-3 py-2 text-primary-foreground"
                                >
                                    {message.text}
                                </p>
                            ) : (
                                <div
                                    key={message.id}
                                    className="max-w-[92%] space-y-2 rounded-xl bg-muted px-3 py-2 text-foreground"
                                >
                                    {message.reply.paragraphs.map((paragraph) => (
                                        <p key={paragraph}>{paragraph}</p>
                                    ))}
                                    {message.reply.bullets.length > 0 && (
                                        <ul className="list-disc space-y-1 pl-4 text-muted-foreground">
                                            {message.reply.bullets.map((bullet) => (
                                                <li key={bullet}>{bullet}</li>
                                            ))}
                                        </ul>
                                    )}
                                    {message.reply.links.length > 0 && (
                                        <p className="flex flex-wrap gap-x-3 gap-y-1 pt-1">
                                            {message.reply.links.map((link) => (
                                                <ReplyLink
                                                    key={link.href}
                                                    {...link}
                                                    onNavigate={() => setOpen(false)}
                                                />
                                            ))}
                                        </p>
                                    )}
                                </div>
                            ),
                        )}

                        {pending && (
                            <p className="flex w-fit items-center gap-2 rounded-xl bg-muted px-3 py-2 text-muted-foreground">
                                <Spinner /> Thinking…
                            </p>
                        )}
                        {error && (
                            <p className="rounded-xl border border-destructive/40 px-3 py-2 text-destructive">
                                {error}
                            </p>
                        )}
                    </div>

                    {suggestions.length > 0 && !pending && (
                        <div className="flex flex-wrap gap-2 border-t border-border px-4 py-3">
                            {suggestions.map((suggestion) => (
                                <button
                                    key={suggestion}
                                    type="button"
                                    onClick={() => void send(suggestion)}
                                    className="rounded-full border border-border px-3 py-1 text-xs text-muted-foreground transition-colors hover:border-border-strong hover:text-foreground"
                                >
                                    {suggestion}
                                </button>
                            ))}
                        </div>
                    )}

                    <form onSubmit={submit} className="flex gap-2 border-t border-border p-3" noValidate>
                        <label htmlFor={`${panelId}-question`} className="sr-only">
                            Your question
                        </label>
                        <Input
                            id={`${panelId}-question`}
                            ref={input}
                            value={question}
                            maxLength={300}
                            autoComplete="off"
                            placeholder="Ask a question…"
                            onChange={(event) => setQuestion(event.target.value)}
                        />
                        <Button
                            type="submit"
                            size="icon"
                            aria-label="Send question"
                            disabled={pending || !question.trim()}
                        >
                            <Send aria-hidden />
                        </Button>
                    </form>
                </section>
            )}

            <Button
                size="lg"
                className="rounded-full shadow-lg"
                aria-expanded={open}
                aria-controls={open ? panelId : undefined}
                onClick={() => setOpen((value) => !value)}
            >
                {open ? <X aria-hidden /> : <AppLogoIcon className="size-5" />}
                {open ? 'Close' : `Ask about ${firstName}`}
            </Button>
        </div>
    );
}

function ReplyLink({ label, href, onNavigate }: { label: string; href: string; onNavigate: () => void }) {
    const className = cn('inline-flex items-center gap-1 font-medium text-primary underline-offset-4 hover:underline');

    if (href.startsWith('/')) {
        return (
            <Link href={href} className={className} onClick={onNavigate}>
                {label}
            </Link>
        );
    }

    return (
        <a
            href={href}
            target={href.startsWith('mailto:') ? undefined : '_blank'}
            rel="noreferrer"
            className={className}
        >
            {label}
            {!href.startsWith('mailto:') && <ArrowUpRight className="size-3.5" aria-hidden />}
        </a>
    );
}
