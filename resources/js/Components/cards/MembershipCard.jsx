import CTAButton from '@/components/common/CTAButton';

export default function MembershipCard({ membership }) {
    return (
        <article className="surface-panel overflow-hidden">
            <div className="bg-primary p-6 text-white">
                <p className="text-[10px] font-bold uppercase tracking-athletic text-accent">
                    {membership.tagline}
                </p>
                <h3 className="mt-3 font-display text-3xl font-bold uppercase">
                    {membership.name}
                </h3>
            </div>
            <div className="space-y-6 p-6">
                <div className="font-display text-4xl font-bold text-primary">
                    {membership.price}
                </div>
                <ul className="space-y-3 text-sm text-gray-600">
                    {membership.benefits.map((benefit) => (
                        <li key={benefit} className="flex items-start gap-3">
                            <span className="material-symbols-outlined text-accent">check_circle</span>
                            <span>{benefit}</span>
                        </li>
                    ))}
                </ul>
                <CTAButton variant="primary" className="w-full">
                    {membership.ctaLabel ?? 'Unirme'}
                </CTAButton>
            </div>
        </article>
    );
}
