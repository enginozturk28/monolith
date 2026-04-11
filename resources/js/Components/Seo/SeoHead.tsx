import { Head, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import type { SharedProps } from '@/types/global';

type Props = {
    title?: string;
    description?: string;
    canonical?: string;
    /** og:type — varsayılan "website", makale için "article" kullanılır */
    type?: 'website' | 'article';
    /** og:image - tam URL */
    image?: string | null;
    /** Article-spesifik */
    publishedTime?: string | null;
    modifiedTime?: string | null;
    author?: string | null;
    /** schema.org JSON-LD — page-spesifik structured data */
    jsonLd?: Record<string, unknown> | Array<Record<string, unknown>>;
    /** Ek child meta tag'leri */
    children?: ReactNode;
};

/**
 * SeoHead — her sayfa için merkezi SEO meta tag bileşeni.
 *
 * Otomatik olarak şu meta tag'leri basıyor:
 * - <title> (Inertia title callback ile " — Loğoğlu Hukuk Bürosu" eklenir)
 * - description
 * - canonical link
 * - Open Graph: title, description, type, url, site_name, image, locale
 * - Twitter Card: card, title, description, image
 * - schema.org JSON-LD: WebSite (her zaman) + LegalService (her zaman) +
 *   page-spesifik (jsonLd prop ile geçilir, örn. Article veya Service)
 *
 * Boş veya null değerler render edilmez. Bu sayede her sayfa sadece kendi
 * spesifik meta'larını geçer, diğerleri varsayılan kalır.
 */
export default function SeoHead({
    title,
    description,
    canonical,
    type = 'website',
    image,
    publishedTime,
    modifiedTime,
    author,
    jsonLd,
    children,
}: Props) {
    const { site, url: pageUrl } = usePage<SharedProps>().props as SharedProps & { url?: string };
    const { url: currentUrl } = usePage();

    const siteName = site.name ?? 'Loğoğlu Hukuk Bürosu';
    const finalDescription = description ?? site.tagline ?? '';
    const baseUrl = typeof window !== 'undefined' ? window.location.origin : 'https://eloboostop.com';
    const finalCanonical = canonical ?? (baseUrl + currentUrl);

    // og:image mutlak URL olmalı — relative path verilmişse baseUrl ekle
    const resolveImageUrl = (img: string | null | undefined): string | null => {
        if (!img) return null;
        if (img.startsWith('http://') || img.startsWith('https://')) return img;
        return baseUrl + (img.startsWith('/') ? img : '/' + img);
    };
    const finalImage = resolveImageUrl(image ?? site.hero_image_path);

    // Site genelinde geçerli structured data — her sayfada basılır
    const baseJsonLd: Array<Record<string, unknown>> = [
        {
            '@context': 'https://schema.org',
            '@type': 'LegalService',
            name: siteName,
            description: site.tagline,
            url: baseUrl,
            telephone: site.phone,
            email: site.email,
            address: site.address
                ? {
                      '@type': 'PostalAddress',
                      streetAddress: site.address,
                      addressLocality: 'Kadıköy',
                      addressRegion: 'İstanbul',
                      addressCountry: 'TR',
                  }
                : undefined,
            ...(site.linkedin_url || site.x_url || site.instagram_url
                ? {
                      sameAs: [site.linkedin_url, site.x_url, site.instagram_url].filter(Boolean),
                  }
                : {}),
        },
        {
            '@context': 'https://schema.org',
            '@type': 'WebSite',
            name: siteName,
            url: baseUrl,
        },
    ];

    // Page-spesifik JSON-LD'yi temel listeye ekle
    const allJsonLd = jsonLd ? [...baseJsonLd, ...(Array.isArray(jsonLd) ? jsonLd : [jsonLd])] : baseJsonLd;

    return (
        <Head title={title}>
            {finalDescription && <meta name="description" content={finalDescription} />}
            <link rel="canonical" href={finalCanonical} />

            {/* Open Graph — og:title ham (suffix yok), site_name ayrı meta'da */}
            <meta property="og:type" content={type} />
            <meta property="og:url" content={finalCanonical} />
            <meta property="og:site_name" content={siteName} />
            <meta property="og:locale" content="tr_TR" />
            {title && <meta property="og:title" content={title} />}
            {finalDescription && <meta property="og:description" content={finalDescription} />}
            {finalImage && <meta property="og:image" content={finalImage} />}

            {/* Twitter Card */}
            <meta name="twitter:card" content={finalImage ? 'summary_large_image' : 'summary'} />
            {title && <meta name="twitter:title" content={title} />}
            {finalDescription && <meta name="twitter:description" content={finalDescription} />}
            {finalImage && <meta name="twitter:image" content={finalImage} />}

            {/* Article meta */}
            {type === 'article' && publishedTime && (
                <meta property="article:published_time" content={publishedTime} />
            )}
            {type === 'article' && modifiedTime && (
                <meta property="article:modified_time" content={modifiedTime} />
            )}
            {type === 'article' && author && <meta property="article:author" content={author} />}

            {/* JSON-LD structured data */}
            <script type="application/ld+json">
                {JSON.stringify(allJsonLd, null, 0)}
            </script>

            {children}
        </Head>
    );
}
