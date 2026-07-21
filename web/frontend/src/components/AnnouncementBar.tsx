import { Container, Button } from '@veraguas/ui';

export interface AnnouncementBarProps {
    clubLabel?: string;
    matchLabel?: string | null;
    matchText?: string | null;
}

/** Equivalente tipado de TopTicker.jsx del frontend actual. */
export function AnnouncementBar({ clubLabel = 'VERAGUAS UNITED FC', matchLabel, matchText }: AnnouncementBarProps) {
    const hasMatch = matchLabel !== null && matchLabel !== undefined && matchText;

    return (
        <header className="fixed left-0 right-0 top-0 z-ticker h-10 bg-primary text-white shadow-ticker">
            <Container className="flex h-full items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <span className="font-display text-sm font-bold uppercase tracking-wider">{clubLabel}</span>
                    {hasMatch ? (
                        <div className="relative hidden items-center gap-2 overflow-hidden rounded-sm bg-accent px-3 py-0.5 md:flex">
                            <div className="pointer-events-none absolute inset-0 animate-shimmer bg-gradient-to-r from-transparent via-white/40 to-transparent" />
                            <span className="relative h-2 w-2 rounded-full bg-red-500">
                                <span className="absolute inset-0 animate-ping rounded-full bg-red-500 opacity-75" />
                            </span>
                            <span className="relative text-[10px] font-bold uppercase text-primary">
                                {matchLabel}: {matchText}
                            </span>
                        </div>
                    ) : (
                        <div className="hidden items-center gap-2 rounded-sm bg-white/10 px-3 py-0.5 md:flex">
                            <span className="text-[10px] font-bold uppercase text-white/60">
                                PRÓXIMO PARTIDO POR ANUNCIAR
                            </span>
                        </div>
                    )}
                </div>
                <Button as="a" href="/boletos" variant="ghost" size="sm" className="rounded-sm bg-white text-primary hover:bg-accent hover:text-white">
                    COMPRAR ENTRADAS
                </Button>
            </Container>
        </header>
    );
}
