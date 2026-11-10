import { Head, usePage } from '@inertiajs/react';

type SeoProps = {
    title?: string;
    description?: string;
    /** Absolute or root-relative image URL for social cards. */
    image?: string | null;
    type?: 'website' | 'article' | 'profile';
};

/** Per-page <title>, meta description, canonical and Open Graph tags. */
export function Seo({ title, description, image, type = 'website' }: SeoProps) {
    const page = usePage();
    const { appUrl, site } = page.props;
    const name = site.name;
    const canonical = `${appUrl}${page.url.split('?')[0]}`;
    const fullTitle = title ? `${title} · ${name}` : name;
    const imageUrl = image ? (image.startsWith('http') ? image : `${appUrl}${image}`) : null;

    return (
        <Head title={title}>
            {description && <meta head-key="description" name="description" content={description} />}
            <link head-key="canonical" rel="canonical" href={canonical} />
            <meta head-key="og:type" property="og:type" content={type} />
            <meta head-key="og:title" property="og:title" content={fullTitle} />
            <meta head-key="og:url" property="og:url" content={canonical} />
            <meta head-key="og:site_name" property="og:site_name" content={name} />
            {description && <meta head-key="og:description" property="og:description" content={description} />}
            {imageUrl && <meta head-key="og:image" property="og:image" content={imageUrl} />}
            <meta head-key="twitter:card" name="twitter:card" content={imageUrl ? 'summary_large_image' : 'summary'} />
        </Head>
    );
}
