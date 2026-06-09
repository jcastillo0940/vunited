import CTAButton from '@/components/common/CTAButton';

export default function MembershipBanner({ membership }) {
    return (
        <section className="group relative overflow-hidden rounded-lg bg-primary p-10 shadow-xl">
            <div className="relative z-10">
                <h3 className="mb-4 font-display text-2xl font-bold uppercase tracking-tight text-white">
                    {membership.title}
                </h3>
                <p className="mb-10 text-sm leading-relaxed text-white/80">
                    {membership.description}
                </p>
                <CTAButton size="md" className="px-8 py-4 text-xs tracking-[0.2em] shadow-lg">
                    {membership.ctaLabel}
                </CTAButton>
            </div>
        </section>
    );
}
