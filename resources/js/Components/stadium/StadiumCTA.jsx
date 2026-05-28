import CTAButton from '@/components/common/CTAButton';

export default function StadiumCTA({ cta }) {
    return (
        <section className="section-space bg-primary">
            <div className="page-shell max-w-5xl">
                <div className="rounded-[32px] border border-white/10 bg-white/10 p-10 text-center text-white shadow-xl backdrop-blur-sm md:p-16">
                    <h2 className="font-display text-4xl font-black uppercase tracking-tight md:text-5xl">
                        {cta.title}
                    </h2>
                    <p className="mx-auto mt-6 max-w-3xl text-lg leading-relaxed text-white/85">
                        {cta.description}
                    </p>
                    <div className="mt-10">
                        <CTAButton as="a" href={cta.actionHref} size="lg" className="font-display text-xl shadow-xl">
                            {cta.actionLabel}
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
