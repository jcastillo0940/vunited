import CTAButton from '@/components/common/CTAButton';

export default function TransparencyCTA({ transparency }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-4xl">
                <div className="relative overflow-hidden rounded-[28px] border-l-8 border-primary bg-white p-10 shadow-2xl md:p-16">
                    <div className="absolute right-0 top-0 p-8 opacity-5">
                        <span className="material-symbols-outlined text-[150px]">policy</span>
                    </div>

                    <div className="relative z-10 flex flex-col items-start gap-8 md:flex-row md:gap-10">
                        <div className="rounded-full bg-primary/5 p-4">
                            <span
                                className="material-symbols-outlined text-5xl text-primary"
                                style={{ fontVariationSettings: "'FILL' 1" }}
                            >
                                verified_user
                            </span>
                        </div>

                        <div>
                            <h2 className="font-display text-3xl font-bold uppercase text-primary">
                                {transparency.title}
                            </h2>
                            <p className="mt-6 text-lg leading-relaxed text-text-main">
                                {transparency.description}
                            </p>
                            <div className="mt-8">
                                <CTAButton
                                    as={transparency.action.href ? 'a' : 'button'}
                                    href={transparency.action.href ?? undefined}
                                    type={transparency.action.href ? undefined : 'button'}
                                    variant="primary"
                                    size="lg"
                                >
                                    {transparency.action.label}
                                </CTAButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
