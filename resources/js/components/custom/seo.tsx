import { Head, usePage } from '@inertiajs/react';
import { feed } from '@/routes/blog';
import type { SiteProfile } from '@/types';

type SeoProps = {
    title?: string;
    description?: string | null;
    /** Absolute or root-relative image URL for social cards (e.g. a project screenshot). */
    image?: string | null;
    /** Generated share card for this page; used when there is no `image`, ahead of the site-wide image. */
    card?: string | null;
    type?: 'website' | 'article' | 'profile';
};

const absolute = (appUrl: string, url: string): string => (url.startsWith('http') ? url : `${appUrl}${url}`);

/**
 * Per-page <title>, meta description, canonical, Open Graph and RSS discovery tags. Anything a page
 * doesn't set falls back to the site-wide SEO defaults from Settings.
 */
export function Seo({ title, description, image, card, type = 'website' }: SeoProps) {
    const page = usePage();
    const { appUrl, site } = page.props;
    const name = site.name;
    const canonical = `${appUrl}${page.url.split('?')[0]}`;
    const fullTitle = title ? `${title} · ${name}` : (site.seo.title ?? name);
    const metaDescription = description ?? site.seo.description ?? site.tagline;
    const imageSource = image ?? card ?? site.seo.image_url;
    const imageUrl = imageSource ? absolute(appUrl, imageSource) : null;

    return (
        <Head title={title}>
            {metaDescription && <meta head-key="description" name="description" content={metaDescription} />}
            <link head-key="canonical" rel="canonical" href={canonical} />
            <meta head-key="og:type" property="og:type" content={type} />
            <meta head-key="og:title" property="og:title" content={fullTitle} />
            <meta head-key="og:url" property="og:url" content={canonical} />
            <meta head-key="og:site_name" property="og:site_name" content={name} />
            {metaDescription && <meta head-key="og:description" property="og:description" content={metaDescription} />}
            {imageUrl && <meta head-key="og:image" property="og:image" content={imageUrl} />}
            {imageUrl && <meta head-key="og:image:alt" property="og:image:alt" content={fullTitle} />}
            <meta head-key="twitter:card" name="twitter:card" content={imageUrl ? 'summary_large_image' : 'summary'} />
            {site.blog_enabled && (
                <link
                    head-key="rss"
                    rel="alternate"
                    type="application/rss+xml"
                    title={`${name} · Blog`}
                    href={`${appUrl}${feed.url()}`}
                />
            )}
        </Head>
    );
}

/**
 * Structured data (schema.org JSON-LD) for search engines. `<` is escaped so content can never
 * close the script tag.
 */
export function JsonLd({ id, data }: { id: string; data: Record<string, unknown> }) {
    const json = JSON.stringify({ '@context': 'https://schema.org', ...data }).replace(/</g, '\\u003c');

    return (
        <Head>
            <script head-key={`ld-${id}`} type="application/ld+json" dangerouslySetInnerHTML={{ __html: json }} />
        </Head>
    );
}

/** schema.org Person for the site owner, built only from what Settings contains. */
export function personSchema(site: SiteProfile, appUrl: string): Record<string, unknown> {
    const sameAs = site.socials.filter((social) => social.url.startsWith('http')).map((social) => social.url);

    return {
        '@type': 'Person',
        '@id': `${appUrl}/#person`,
        name: site.name,
        jobTitle: site.headline,
        url: appUrl,
        ...(site.short_bio ? { description: site.short_bio } : {}),
        ...(site.avatar_url ? { image: absolute(appUrl, site.avatar_url) } : {}),
        ...(site.location ? { address: { '@type': 'PostalAddress', addressLocality: site.location } } : {}),
        ...(sameAs.length > 0 ? { sameAs } : {}),
    };
}
