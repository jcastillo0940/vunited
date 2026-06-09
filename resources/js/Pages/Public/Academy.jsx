import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import AcademyHero from '@/components/academy/AcademyHero';
import AcademyIntro from '@/components/academy/AcademyIntro';
import AcademyCategories from '@/components/academy/AcademyCategories';
import AcademyStats from '@/components/academy/AcademyStats';
import AcademyProcess from '@/components/academy/AcademyProcess';
import AcademyCTA from '@/components/academy/AcademyCTA';
import academyMock from '@/mocks/academyMock';

export default function Academy() {
    const settings = useLayoutSettings();
    const [activeCategoryId, setActiveCategoryId] = useState(academyMock.categories[0].id);

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
        () => settings.global_seo_title || 'Fuerzas Basicas | Veraguas United FC',
        [settings.global_seo_title],
    );

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="SOCIO INDIO"
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <AcademyHero hero={academyMock.hero} />
                <AcademyStats stats={academyMock.impactStats} />
                <AcademyIntro intro={academyMock.intro} />
                <AcademyCategories
                    categories={academyMock.categories}
                    activeCategoryId={activeCategoryId}
                    onCategoryChange={setActiveCategoryId}
                />
                <AcademyProcess steps={academyMock.process} />
                <AcademyCTA cta={academyMock.finalCta} />
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
        active: item.url === '/fuerzas-basicas',
        children: toMenuLinks(item.children ?? []),
    }));
}
