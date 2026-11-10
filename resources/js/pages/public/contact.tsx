import { usePage } from '@inertiajs/react';
import { Mail, MapPin } from 'lucide-react';
import { PageHeader } from '@/components/shared/page-header';
import { Seo } from '@/components/shared/seo';
import { SocialLinks } from '@/components/shared/social-links';
import { ContactForm } from '@/features/contact/components/contact-form';

export default function Contact() {
    const { site } = usePage().props;

    return (
        <>
            <Seo title="Contact" description={`Get in touch with ${site.name}.`} />
            <div className="grid gap-12 lg:grid-cols-[1fr_1.4fr]">
                <div className="space-y-8">
                    <PageHeader
                        size="lg"
                        title="Let's talk"
                        description="Have a project, a role, or a question? Send a message and I'll reply within a couple of days."
                    />
                    <dl className="space-y-3 text-sm">
                        {site.email && (
                            <div className="flex items-center gap-3">
                                <dt>
                                    <Mail className="size-4 text-muted-foreground" aria-label="Email" />
                                </dt>
                                <dd>
                                    <a href={`mailto:${site.email}`} className="hover:text-brand">
                                        {site.email}
                                    </a>
                                </dd>
                            </div>
                        )}
                        {site.location && (
                            <div className="flex items-center gap-3">
                                <dt>
                                    <MapPin className="size-4 text-muted-foreground" aria-label="Location" />
                                </dt>
                                <dd>{site.location}</dd>
                            </div>
                        )}
                    </dl>
                    <SocialLinks className="-ml-2" />
                </div>
                <div className="relative rounded-xl border p-6 sm:p-8">
                    <ContactForm />
                </div>
            </div>
        </>
    );
}
