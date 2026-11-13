import { usePage } from '@inertiajs/react';
import { Hint } from '@/components/custom/hint';
import { SocialIcon } from '@/components/custom/social-icon';
import { cn } from '@/lib/utils';

/** The owner's social profiles from the shared `site` prop, as icon links with tooltips. */
export function SocialLinks({ className }: { className?: string }) {
    const { socials } = usePage().props.site;

    if (socials.length === 0) {
        return null;
    }

    return (
        <ul className={cn('flex items-center gap-1', className)}>
            {socials.map((social) => {
                const external = social.platform !== 'email';

                return (
                    <li key={social.url}>
                        <Hint label={social.label}>
                            <a
                                href={social.url}
                                target={external ? '_blank' : undefined}
                                rel={external ? 'noreferrer me' : undefined}
                                aria-label={external ? `${social.label} (opens in a new tab)` : social.label}
                                className="inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors duration-150 hover:bg-accent hover:text-foreground"
                            >
                                <SocialIcon platform={social.platform} className="size-[18px]" />
                            </a>
                        </Hint>
                    </li>
                );
            })}
        </ul>
    );
}
