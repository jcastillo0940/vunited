import { Container, Grid, Card } from '@veraguas/ui';
import { Link } from 'react-router-dom';

export interface NewsSummary {
    slug: string;
    title: string;
    excerpt: string;
    publishedAt: string;
}

export interface NewsListLayoutProps {
    items: NewsSummary[];
}

/** "Layout de noticias" — listado. */
export function NewsListLayout({ items }: NewsListLayoutProps) {
    return (
        <div className="section-space">
            <Container>
                <p className="display-kicker mb-2">Actualidad</p>
                <h1 className="section-heading mb-10">Noticias</h1>
                <Grid cols={3}>
                    {items.map((item) => (
                        <Card key={item.slug} className="flex flex-col gap-3">
                            <time className="text-xs uppercase tracking-wide text-text-main/50" dateTime={item.publishedAt}>
                                {item.publishedAt}
                            </time>
                            <h2 className="font-display text-lg font-bold uppercase text-primary">{item.title}</h2>
                            <p className="text-sm text-text-main/70">{item.excerpt}</p>
                            <Link to={`/noticias/${item.slug}`} className="mt-auto text-sm font-semibold text-accent hover:underline">
                                Leer más
                            </Link>
                        </Card>
                    ))}
                </Grid>
            </Container>
        </div>
    );
}

export interface NewsDetailLayoutProps {
    title: string;
    publishedAt: string;
    body: string;
}

/** "Layout de noticias" — detalle. */
export function NewsDetailLayout({ title, publishedAt, body }: NewsDetailLayoutProps) {
    return (
        <div className="section-space">
            <Container className="max-w-3xl">
                <time className="display-kicker" dateTime={publishedAt}>
                    {publishedAt}
                </time>
                <h1 className="section-heading mt-2">{title}</h1>
                <div className="mt-8 space-y-4 text-base leading-relaxed text-text-main">{body}</div>
            </Container>
        </div>
    );
}
