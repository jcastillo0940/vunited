import CTAButton from '@/components/common/CTAButton';

export default function TicketCard({ ticket }) {
    return (
        <article className="surface-card p-6">
            <div className="space-y-4">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="display-kicker">Zona disponible</p>
                        <h3 className="font-display text-2xl font-bold uppercase text-primary">
                            {ticket.name}
                        </h3>
                    </div>
                    <span className="font-display text-3xl font-bold text-primary">
                        {ticket.price}
                    </span>
                </div>
                <p className="text-sm leading-relaxed text-gray-600">
                    {ticket.description}
                </p>
                <CTAButton variant="outline" className="w-full">
                    {ticket.ctaLabel ?? 'Seleccionar'}
                </CTAButton>
            </div>
        </article>
    );
}
