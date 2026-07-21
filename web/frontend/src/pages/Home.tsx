import { Container, Button, Card } from '@veraguas/ui';
import { Seo } from '../seo/Seo';

// Copy real tomado de resources/js/mocks/homeMock.js (frontend actual) — no
// se redacta contenido nuevo, se reutiliza el mismo mensaje de marca.
const HERO = {
    badge: 'PRÓXIMO ENCUENTRO',
    title: 'RUGE EL INDIO,',
    highlight: 'SOMOS VERAGUAS',
    description: 'Únete a la pasión en el Estadio Atalaya. Defendamos juntos nuestra tierra en el Clausura.',
    primaryAction: { label: 'COMPRAR BOLETOS', href: '/boletos' },
    secondaryAction: { label: 'HAZTE MIEMBRO', href: '/fanclub' },
};

const NEXT_MATCH = {
    label: 'PRÓXIMO PARTIDO',
    home: 'Herrera FC',
    away: 'Veraguas United',
    note: 'DERBI DE PROVINCIAS',
    date: '19 OCT, 19:00',
};

export function Home() {
    return (
        <>
            <Seo
                title="Inicio"
                description="Sitio oficial del Veraguas United FC. Noticias, boletos, tienda y todo sobre el club."
                canonicalPath="/"
            />
            <section className="relative flex min-h-[32rem] items-center overflow-hidden bg-primary text-white">
                <Container className="relative py-24">
                    <p className="display-kicker text-white/80">{HERO.badge}</p>
                    <h1 className="mt-4 max-w-2xl font-display text-5xl font-bold uppercase leading-none tracking-tight md:text-6xl">
                        {HERO.title}
                        <br />
                        <span className="text-accent">{HERO.highlight}</span>
                    </h1>
                    <p className="mt-6 max-w-lg text-lg text-white/80">{HERO.description}</p>
                    <div className="mt-10 flex flex-wrap gap-4">
                        <Button as="a" href={HERO.primaryAction.href} variant="secondary" size="lg">
                            {HERO.primaryAction.label}
                        </Button>
                        <Button as="a" href={HERO.secondaryAction.href} variant="outline" size="lg" className="border-white text-white hover:bg-white/10">
                            {HERO.secondaryAction.label}
                        </Button>
                    </div>
                </Container>
            </section>

            <section className="section-space">
                <Container>
                    <p className="display-kicker mb-2">{NEXT_MATCH.label}</p>
                    <Card className="flex flex-col items-center gap-4 text-center md:flex-row md:justify-between md:text-left">
                        <div>
                            <p className="font-display text-2xl font-bold uppercase text-primary">
                                {NEXT_MATCH.home} <span className="text-accent">vs</span> {NEXT_MATCH.away}
                            </p>
                            <p className="text-sm text-text-main/60">{NEXT_MATCH.note} · {NEXT_MATCH.date}</p>
                        </div>
                        <Button as="a" href="/boletos">
                            Comprar entradas
                        </Button>
                    </Card>
                </Container>
            </section>
        </>
    );
}
