import { Head } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { DotFieldBackground } from '@/components/custom/dot-field-background';
import { SectionLabel } from '@/components/custom/section-label';
import { Button } from '@/components/ui/button';
import { home } from '@/routes';

type Message = { title: string; description: string };

const MESSAGES: Record<number, Message> = {
    400: { title: 'Bad request', description: "The request couldn't be understood. Please go back and try again." },
    403: { title: 'Access denied', description: "You don't have permission to view this page." },
    404: { title: 'Page not found', description: "This page doesn't exist, or it has moved." },
    405: { title: 'Not available', description: "That address can't be opened this way." },
    419: { title: 'Session expired', description: 'Your session timed out. Go back, refresh the page and try again.' },
    429: { title: 'Too many requests', description: 'Please wait a minute before trying again.' },
    500: { title: 'Something broke', description: "An error on my side. It's been logged; please try again shortly." },
    503: {
        title: 'Down for maintenance',
        description: 'The site is being updated. Please check back in a few minutes.',
    },
};

const CLIENT_ERROR: Message = {
    title: 'Something went wrong',
    description: "This page couldn't be shown. Please go back and try again.",
};

/** The message for a status, falling back to a generic one for its class (4xx / 5xx). */
function messageFor(status: number): Message {
    return MESSAGES[status] ?? (status < 500 ? CLIENT_ERROR : MESSAGES[500]);
}

/**
 * Error page for every 4xx/5xx page load (5xx only outside local), rendered by
 * App\\Shared\\Exceptions\\FriendlyErrorResponse. It has no
 * layout on purpose: unknown URLs never pass through the web middleware, so shared props such as
 * `site` aren't available here.
 */
export default function ErrorPage({ status }: { status: number }) {
    const message = messageFor(status);

    return (
        <>
            <Head title={message.title}>
                <meta head-key="robots" name="robots" content="noindex" />
            </Head>
            <main className="relative isolate flex min-h-svh items-center bg-background font-sans text-muted-foreground antialiased">
                <DotFieldBackground />
                <div className="mx-auto w-full max-w-xl space-y-6 px-4 sm:px-6">
                    <SectionLabel>{`error ${status}`}</SectionLabel>
                    <h1 className="text-4xl font-semibold tracking-tight text-foreground md:text-5xl">
                        {message.title}
                    </h1>
                    <p className="text-lg leading-relaxed">{message.description}</p>
                    <Button asChild>
                        <a href={home().url}>
                            <ArrowLeft aria-hidden /> Back to home
                        </a>
                    </Button>
                </div>
            </main>
        </>
    );
}
