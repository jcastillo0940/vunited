import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import StoreHero from '@/components/store/StoreHero';
import ProductFilters from '@/components/store/ProductFilters';
import FeaturedProduct from '@/components/store/FeaturedProduct';
import ProductGrid from '@/components/store/ProductGrid';
import StoreCartPreview from '@/components/store/StoreCartPreview';
import homeMock from '@/mocks/homeMock';
import productsMock from '@/mocks/productsMock';
import cartStorageService from '@/services/cartStorageService';
import productService from '@/services/productService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
    publicPrimaryCta,
} from '@/config/publicNavigation';

const fallbackSettings = {
    site_name: 'Veraguas United FC',
    site_tagline: 'Orgullo de Veraguas',
    primary_logo_url: null,
    secondary_logo_url: null,
    primary_color: '#1D428A',
    accent_color: '#5BC2E7',
    contact_email: 'hola@veraguasunited.test',
    contact_phone: '+507 6000-0000',
    social_links: {
        instagram: 'https://instagram.com/veraguasunited',
        facebook: 'https://facebook.com/veraguasunited',
    },
    global_seo_title: 'Tienda Oficial | Veraguas United FC',
    global_seo_description: 'Tienda oficial del Veraguas United FC. Camisetas, accesorios y más.',
    maintenance_mode: false,
};

export default function Store() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/tienda'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [selectedFilter, setSelectedFilter] = useState('todos');
    const [cartItems, setCartItems] = useState(() => cartStorageService.loadCart());
    const [storeHero, setStoreHero] = useState(productsMock.hero);
    const [membershipBanner, setMembershipBanner] = useState(productsMock.membershipBanner);
    const [catalog, setCatalog] = useState({
        filters: productsMock.filters.map((filter) => ({
            label: filter,
            value: filter === 'Todos' ? 'todos' : filter.toLowerCase().replace(/\s+/g, '-'),
        })),
        featuredProduct: normalizeFeaturedProduct(productsMock.featuredProduct),
        products: productsMock.products.map(normalizeProduct),
    });

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/tienda'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
            }
        }

        loadShell();

        return () => {
            active = false;
        };
    }, [defaultFooterLinks, defaultHeaderLinks]);
    useEffect(() => {
        let active = true;

        async function loadCatalog() {
            try {
                const [categoriesResponse, productsResponse, featuredResponse] = await Promise.all([
                    productService.getCategories(),
                    productService.getProducts(),
                    productService.getFeaturedProduct(),
                ]);

                if (!active) {
                    return;
                }

                const categories = categoriesResponse.data?.data ?? [];
                const products = productsResponse.data?.data ?? [];
                const featuredProduct = featuredResponse.data?.data ?? null;

                setCatalog({
                    filters: [
                        { label: 'Todos', value: 'todos' },
                        ...categories.map((category) => ({
                            label: category.name,
                            value: category.slug,
                        })),
                    ],
                    featuredProduct: featuredProduct ? normalizeApiProduct(featuredProduct) : normalizeFeaturedProduct(productsMock.featuredProduct),
                    products: products.length ? products.map(normalizeApiProduct) : productsMock.products.map(normalizeProduct),
                });
            } catch {
                if (!active) {
                    return;
                }

                setCatalog({
                    filters: productsMock.filters.map((filter) => ({
                        label: filter,
                        value: filter === 'Todos' ? 'todos' : filter.toLowerCase().replace(/\s+/g, '-'),
                    })),
                    featuredProduct: normalizeFeaturedProduct(productsMock.featuredProduct),
                    products: productsMock.products.map(normalizeProduct),
                });
            }
        }

        loadCatalog();

        return () => {
            active = false;
        };
    }, []);

    useEffect(() => {
        cartStorageService.saveCart(cartItems);
    }, [cartItems]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Tienda Oficial | Veraguas United FC',
        [settings.global_seo_title],
    );

    const filteredProducts = useMemo(() => {
        if (selectedFilter === 'todos') {
            return catalog.products;
        }

        return catalog.products.filter((product) => product.categorySlug === selectedFilter);
    }, [catalog.products, selectedFilter]);

    function handleAddToCart(product) {
        setCartItems((current) => {
            const existing = current.find((item) => item.id === product.id);

            if (existing) {
                return current.map((item) =>
                    item.id === product.id
                        ? { ...item, quantity: item.quantity + 1 }
                        : item,
                );
            }

            return [
                ...current,
                {
                    id: product.id,
                    productId: product.id,
                    slug: product.slug ?? null,
                    name: product.name,
                    variant: product.subtitle,
                    price: product.unitPrice,
                    priceLabel: product.price,
                    currency: product.currency ?? 'USD',
                    quantity: 1,
                    imageUrl: product.imageUrl,
                },
            ];
        });
    }

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                settings={settings}
                headerMenu={headerMenu}
                footerMenu={footerMenu}
                legalMenu={publicLegalLinks}
                ticker={homeMock.ticker}
                tickerClubLabel="VERAGUAS UNITED FC"
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <StoreHero
                    hero={storeHero}
                    membershipBanner={membershipBanner}
                    cartCount={cartItems.reduce((total, item) => total + item.quantity, 0)}
                />

                <section id="catalogo" className="pb-20 pt-16">
                    <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                        <FeaturedProduct
                            product={catalog.featuredProduct}
                            onAddToCart={handleAddToCart}
                        />

                        <div className="mt-16 grid grid-cols-1 gap-10 xl:grid-cols-[minmax(0,1fr)_340px]">
                            <div>
                                <div className="mb-8 flex flex-col gap-5 border-l-4 border-accent pl-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h2 className="font-display text-3xl font-bold uppercase text-primary md:text-4xl">
                                            Catálogo United
                                        </h2>
                                        <p className="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                                            Camisetas oficiales, accesorios de grada y piezas de edición especial para vivir la identidad del club.
                                        </p>
                                    </div>
                                    <ProductFilters
                                        filters={catalog.filters}
                                        selectedFilter={selectedFilter}
                                        onSelect={setSelectedFilter}
                                    />
                                </div>

                                <ProductGrid
                                    products={filteredProducts}
                                    onAddToCart={handleAddToCart}
                                />
                            </div>

                            <StoreCartPreview cartItems={cartItems} />
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        ...item,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}

function normalizeProduct(product) {
    return {
        ...product,
        unitPrice: parseMoney(product.salePrice ?? product.price),
        currency: 'USD',
        categorySlug: product.category === 'Edicion especial'
            ? 'edicion-especial'
            : product.category.toLowerCase().replace(/\s+/g, '-'),
        slug: product.slug ?? null,
    };
}

function normalizeFeaturedProduct(product) {
    return {
        ...normalizeProduct(product),
    };
}

function normalizeApiProduct(product) {
    return {
        id: product.id,
        name: product.name,
        category: product.category?.name ?? 'Sin categoria',
        categorySlug: product.category?.slug ?? 'sin-categoria',
        badge: product.badge ?? 'OFICIAL',
        subtitle: product.short_description ?? product.description ?? 'Catalogo oficial del club.',
        price: formatMoney(product.price, product.currency),
        salePrice: product.compare_at_price ? formatMoney(product.price, product.currency) : null,
        compareAtPrice: product.compare_at_price ? formatMoney(product.compare_at_price, product.currency) : null,
        imageUrl: product.image_url,
        ctaLabel: 'Agregar al carrito',
        slug: product.slug,
        unitPrice: Number.parseFloat(product.price ?? 0),
        currency: product.currency ?? 'USD',
        outOfStock: product.out_of_stock,
    };
}

function formatMoney(price, currency = 'USD') {
    const numeric = Number.parseFloat(price ?? 0);

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency,
    }).format(Number.isNaN(numeric) ? 0 : numeric);
}

function parseMoney(value) {
    if (typeof value === 'number') {
        return value;
    }

    const normalized = String(value ?? '0').replace(/[^0-9.-]+/g, '');
    const numeric = Number.parseFloat(normalized);

    return Number.isNaN(numeric) ? 0 : numeric;
}
