import { Link, usePage } from '@inertiajs/react';
import { containers } from '@/components/custom/section';
import { SocialLinks } from '@/components/custom/social-links';
import { footerNavigation } from '@/layouts/public/navigation';
import { cn, toUrl } from '@/lib/utils';

/** Site footer: owner line, secondary links (e.g. uses) and social links. */
export function SiteFooter({ name }: { name: string }) {
    const year = new Date().getFullYear();
    const { site, content } = usePage().props;
    const links = footerNavigation({ blog: site.blog_enabled, ...content });

    return (
        <footer className="border-t border-border">
            <div
                className={cn(
                    containers.page,
                    'flex flex-col gap-6 py-10 sm:flex-row sm:items-center sm:justify-between',
                )}
            >
                <div className="space-y-1">
                    <p className="text-sm text-foreground">
                        © {year} {name}
                    </p>
                    <p className="font-mono text-xs text-muted-foreground">built with laravel · inertia · react</p>
                </div>
                <div className="flex flex-wrap items-center gap-x-6 gap-y-3">
                    {links.length > 0 && (
                        <nav aria-label="Footer">
                            <ul className="flex gap-4">
                                {links.map((item) => (
                                    <li key={toUrl(item.href)}>
                                        <Link
                                            href={item.href}
                                            className="rounded-sm font-mono text-xs text-muted-foreground transition-colors duration-150 hover:text-foreground"
                                        >
                                            {item.title.toLowerCase()}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    )}
                    <SocialLinks className="-ml-2 sm:ml-0" />
                </div>
            </div>
        </footer>
    );
}
