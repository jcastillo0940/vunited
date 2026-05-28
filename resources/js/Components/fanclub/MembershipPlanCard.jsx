import CTAButton from '@/components/common/CTAButton';

export default function MembershipPlanCard({ annualPass, salesCopy }) {
    return (
        <section className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                    <div className="order-2 lg:order-1">
                        <div className="rounded-xl border border-gray-200 bg-surface p-8 shadow-md md:p-12">
                            <div className="mb-10 flex items-start justify-between gap-6">
                                <div>
                                    <h2 className="font-display text-2xl font-bold uppercase text-accent">
                                        {annualPass.eyebrow}
                                    </h2>
                                    <h3 className="font-display text-4xl font-black uppercase italic text-primary">
                                        {annualPass.name}
                                    </h3>
                                </div>
                                <div className="animate-pulse rounded-md bg-accent px-4 py-2 text-xs font-bold uppercase tracking-wider text-white">
                                    {annualPass.badge}
                                </div>
                            </div>

                            <div className="mb-12 space-y-6">
                                {annualPass.bullets.map((bullet) => (
                                    <div
                                        key={bullet.text}
                                        className="flex items-center gap-4 border-b border-gray-200 pb-4"
                                    >
                                        <span className="material-symbols-outlined text-accent">
                                            {bullet.icon}
                                        </span>
                                        <p className="font-semibold text-text-main">{bullet.text}</p>
                                    </div>
                                ))}
                            </div>

                            <div className="mb-10 flex items-end gap-2">
                                <span className="font-display text-6xl font-black text-primary">
                                    {annualPass.price}
                                </span>
                                <span className="mb-2 text-sm font-bold uppercase text-gray-500">
                                    {annualPass.cadence}
                                </span>
                            </div>

                            <CTAButton
                                as="a"
                                href={annualPass.ctaHref}
                                variant="secondary"
                                size="lg"
                                className="w-full justify-center py-5 font-display text-xl tracking-[0.2em] shadow-lg"
                            >
                                {annualPass.ctaLabel}
                            </CTAButton>
                        </div>
                    </div>

                    <div className="order-1 space-y-8 lg:order-2">
                        <span className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                            {salesCopy.eyebrow}
                        </span>
                        <h2 className="font-display text-5xl font-black uppercase leading-tight text-primary md:text-6xl">
                            {salesCopy.title}
                            <br />
                            {salesCopy.highlight}
                        </h2>
                        <p className="text-lg leading-relaxed text-gray-600">{salesCopy.description}</p>
                        <div className="grid grid-cols-2 gap-6 pt-6">
                            {salesCopy.stats.map((stat) => (
                                <div
                                    key={stat.label}
                                    className="rounded-lg border-l-4 border-accent bg-surface p-6 shadow-sm"
                                >
                                    <h4 className="font-display text-4xl font-black text-primary">
                                        {stat.value}
                                    </h4>
                                    <p className="mt-1 text-[11px] font-bold uppercase tracking-widest text-gray-500">
                                        {stat.label}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
