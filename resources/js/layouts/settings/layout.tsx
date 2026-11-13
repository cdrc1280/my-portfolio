import { Link, usePage } from '@inertiajs/react';
import { ShieldCheck, UserRound } from 'lucide-react';
import type { PropsWithChildren } from 'react';
import { FactList, SidePanel, TipList } from '@/components/custom/admin-split';
import Heading from '@/components/custom/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { formatDate } from '@/lib/format';
import { cn, toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: edit(),
        icon: null,
    },
    {
        title: 'Security',
        href: editSecurity(),
        icon: null,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: null,
    },
];

export default function SettingsLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();
    const user = usePage().props.auth.user;

    return (
        <div className="p-4 md:p-6">
            <Heading title="Settings" description="Manage your profile and account settings" />

            <div className="flex flex-col lg:flex-row lg:space-x-12">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav className="flex flex-col space-y-1 space-x-0" aria-label="Settings">
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${toUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start', {
                                    'bg-muted': isCurrentOrParentUrl(item.href),
                                })}
                            >
                                <Link href={item.href}>
                                    {item.icon && <item.icon className="h-4 w-4" />}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="grid min-w-0 flex-1 gap-8 xl:grid-cols-[minmax(0,1fr)_20rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]">
                    <section className="max-w-2xl min-w-0 space-y-12">{children}</section>

                    {user && (
                        <aside className="grid content-start gap-4 xl:sticky xl:top-4 xl:self-start">
                            <SidePanel title="Account" icon={UserRound}>
                                <FactList
                                    facts={[
                                        { term: 'Name', value: user.name },
                                        { term: 'Email', value: user.email },
                                        { term: 'Verified', value: user.email_verified_at ? 'Yes' : 'No' },
                                        {
                                            term: 'Member since',
                                            value: user.created_at ? formatDate(user.created_at) : 'Unknown',
                                        },
                                    ]}
                                />
                            </SidePanel>
                            <SidePanel title="Security" icon={ShieldCheck}>
                                <FactList
                                    facts={[
                                        {
                                            term: 'Two-factor',
                                            value: user.two_factor_enabled ? 'Enabled' : 'Off',
                                        },
                                    ]}
                                />
                                <div className="mt-3">
                                    <TipList
                                        tips={[
                                            'Use a long, random password from a password manager.',
                                            'Turn on two-factor: this account can edit the whole site.',
                                        ]}
                                    />
                                </div>
                            </SidePanel>
                        </aside>
                    )}
                </div>
            </div>
        </div>
    );
}
