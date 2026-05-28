import CTAButton from '@/components/common/CTAButton';

export default function TopTicker({
    fixed = true,
    clubLabel = 'VERAGUAS UNITED FC',
    tickerLabel = null,
    tickerText = null,
    ctaLabel = 'COMPRAR ENTRADAS',
    ctaHref = '/boletos',
}) {
    const hasMatch = tickerLabel !== null && tickerText !== null;

    return (
        <header
            className={[
                fixed ? 'fixed left-0 right-0 top-0 z-50' : 'relative z-10',
                'h-10 bg-primary text-white shadow-sm',
            ].join(' ')}
        >
            <div className="page-shell flex h-full items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <span className="font-display text-sm font-bold uppercase tracking-wider">
                        {clubLabel}
                    </span>
                    {hasMatch ? (
                        <div className="hidden items-center gap-2 rounded-sm bg-accent px-3 py-0.5 md:flex">
                            <span className="h-2 w-2 animate-pulse rounded-full bg-red-500" />
                            <span className="text-[10px] font-bold uppercase text-primary">
                                {tickerLabel}: {tickerText}
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
                <CTAButton
                    as="a"
                    href={ctaHref}
                    variant="ghost"
                    size="sm"
                    className="rounded-sm bg-white px-4 py-1 text-[10px] text-primary hover:bg-accent hover:text-white"
                >
                    {ctaLabel}
                </CTAButton>
            </div>
        </header>
    );
}
