import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import TryoutsHero from '@/components/tryouts/TryoutsHero';
import TryoutsForm from '@/components/tryouts/TryoutsForm';
import TryoutsInfoCards from '@/components/tryouts/TryoutsInfoCards';
import tryoutsMock from '@/mocks/tryoutsMock';

export default function Tryouts() {
    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                ]);

                if (!active) {
                    return;
                }

            } catch {
                if (!active) {
                    return;
                }

            }
        }

        loadShell();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Pruebas | Veraguas United FC',
        [settings.global_seo_title],
    );

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
                mainClassName="pt-32"
            >
                <TryoutsHero hero={tryoutsMock.hero} />
                <TryoutsForm positionOptions={tryoutsMock.positionOptions} />
                <TryoutsInfoCards cards={tryoutsMock.infoCards} />
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        label: item.label,
        url: item.url,
        active: item.url === '/pruebas',
        children: toMenuLinks(item.children ?? []),
    }));
}
