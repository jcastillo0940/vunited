#!/usr/bin/env node
// Genera public/robots.txt y public/sitemap.xml antes del build, según el
// ambiente (VITE_ENV). Se ejecuta como paso "prebuild" — ver package.json.
import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const __dirname = dirname(fileURLToPath(import.meta.url));
const publicDir = join(__dirname, '..', 'public');
const env = process.env.VITE_ENV ?? 'local';
const SITE_URL = 'https://united.wp-pa.com';

const isProduction = env === 'production';

const robots = isProduction
    ? `User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ${SITE_URL}/sitemap.xml\n`
    : `User-agent: *\nDisallow: /\n`;

writeFileSync(join(publicDir, 'robots.txt'), robots);

const routes = [
    '/',
    '/noticias',
    '/calendario',
    '/directiva',
    '/plantilla',
    '/fuerzas-basicas',
    '/pruebas',
    '/estadio',
    '/patrocinadores',
    '/fanfest',
    '/expedicion-india',
];

const urlEntries = routes
    .map((path) => `  <url><loc>${SITE_URL}${path}</loc></url>`)
    .join('\n');

const sitemap = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urlEntries}\n</urlset>\n`;

writeFileSync(join(publicDir, 'sitemap.xml'), sitemap);

console.log(`[seo] robots.txt (${env}) y sitemap.xml generados en ${publicDir}`);
