import CTAButton from '@/components/common/CTAButton';

export default function PresidentBlock({ president }) {
    return (
        <section className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="grid items-center gap-12 md:grid-cols-[1fr_1fr] md:gap-16">
                    <div className="rounded-[28px] bg-surface p-3 shadow-panel">
                        <img
                            src={president.imageUrl}
                            alt={president.name}
                            className="h-[550px] w-full rounded-xl object-cover"
                        />
                    </div>

                    <div>
                        <div className="mb-8">
                            <p className="text-lg font-bold uppercase tracking-[0.3em] text-accent">
                                {president.role}
                            </p>
                            <h2 className="mt-3 font-display text-5xl font-bold uppercase text-primary">
                                {president.name}
                            </h2>
                            <p className="mt-2 text-sm font-bold uppercase tracking-[0.25em] text-primary/60">
                                {president.title}
                            </p>
                        </div>

                        <p className="text-lg leading-relaxed text-text-main">{president.message}</p>

                        <div className="mt-10 flex flex-wrap items-center gap-4">
                            <CTAButton
                                as={president.primaryAction.href ? 'a' : 'button'}
                                href={president.primaryAction.href ?? undefined}
                                type={president.primaryAction.href ? undefined : 'button'}
                                variant="secondary"
                                size="lg"
                            >
                                {president.primaryAction.label}
                            </CTAButton>
                            <div className="flex gap-3">
                                {president.socialActions.map((action) => (
                                    <span
                                        key={action.id}
                                        className="flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary shadow-sm transition-all hover:bg-accent hover:text-white"
                                    >
                                        <span className="material-symbols-outlined">{action.icon}</span>
                                    </span>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
