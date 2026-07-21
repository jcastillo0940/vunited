import { useEffect } from 'react';

export interface SeoProps {
    title: string;
    description: string;
    canonicalPath: string;
    image?: string;
    noIndex?: boolean;
}

const SITE_NAME = 'Veraguas United FC';
const SITE_URL = 'https://united.wp-pa.com';

function setMeta(attr: 'name' | 'property', key: string, content: string) {
    let el = document.head.querySelector<HTMLMetaElement>(`meta[${attr}="${key}"]`);
    if (!el) {
        el = document.createElement('meta');
        el.setAttribute(attr, key);
        document.head.appendChild(el);
    }
    el.setAttribute('content', content);
}

function setCanonical(href: string) {
    let link = document.head.querySelector<HTMLLinkElement>('link[rel="canonical"]');
    if (!link) {
        link = document.createElement('link');
        link.setAttribute('rel', 'canonical');
        document.head.appendChild(link);
    }
    link.setAttribute('href', href);
}

/**
 * SEO por página: title, meta description, canonical y Open Graph.
 * Sin dependencias externas — manipula document.head directamente, lo cual
 * es suficiente para una SPA que además se sirve pre-renderizada por página
 * a futuro (no se descarta SSR, pero no es parte de esta fase).
 */
export function Seo({ title, description, canonicalPath, image, noIndex }: SeoProps) {
    useEffect(() => {
        const fullTitle = `${title} · ${SITE_NAME}`;
        document.title = fullTitle;
        setMeta('name', 'description', description);
        setCanonical(`${SITE_URL}${canonicalPath}`);
        setMeta('property', 'og:title', fullTitle);
        setMeta('property', 'og:description', description);
        setMeta('property', 'og:type', 'website');
        setMeta('property', 'og:url', `${SITE_URL}${canonicalPath}`);
        if (image) setMeta('property', 'og:image', image);

        const robots = document.head.querySelector<HTMLMetaElement>('meta[name="robots"]');
        if (robots) robots.setAttribute('content', noIndex ? 'noindex,nofollow' : 'index,follow');
    }, [title, description, canonicalPath, image, noIndex]);

    return null;
}
