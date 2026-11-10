import { usePage } from '@inertiajs/react';
import { SocialIcon } from '@/components/shared/social-icon';
import { cn } from '@/lib/utils';

/** The owner's social profiles from the shared `site` prop, as icon links. */
export function SocialLinks({ className }: { className?: string }) {
    const { socials } = usePage().props.site;

    if (socials.length === 0) {
        return null;
    }

    return (
        <ul className={cn('flex items-center gap-1', className)}>
            {socials.map((social) => (
                <li key={social.url}>
                    <a
                        href={social.url}
                        target={social.platform === 'email' ? undefined : '_blank'}
                        rel="noreferrer me"
                        className="inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    >
                        <SocialIcon platform={social.platform} className="size-4" />
                        <span className="sr-only">{social.label}</span>
                    </a>
                </li>
            ))}
        </ul>
    );
}
