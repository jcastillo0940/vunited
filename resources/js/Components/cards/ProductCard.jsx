import CTAButton from '@/components/common/CTAButton';

export default function ProductCard({ product }) {
    return (
        <article className="surface-card group overflow-hidden transition-shadow hover:shadow-panel">
            <div className="relative aspect-square bg-surface">
                {product.imageUrl ? (
                    <img
                        src={product.imageUrl}
                        alt={product.name}
                        className="h-full w-full object-cover"
                    />
                ) : (
                    <div className="flex h-full items-center justify-center text-gray-300">
                        <span className="material-symbols-outlined text-6xl">checkroom</span>
                    </div>
                )}
            </div>
            <div className="space-y-4 p-6">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h3 className="font-display text-xl font-bold uppercase text-primary">
                            {product.name}
                        </h3>
                        <p className="mt-2 text-sm text-gray-500">{product.subtitle}</p>
                    </div>
                    <span className="font-display text-2xl font-bold text-primary">
                        {product.price}
                    </span>
                </div>
                <CTAButton variant="primary" className="w-full">
                    {product.ctaLabel ?? 'Ver producto'}
                </CTAButton>
            </div>
        </article>
    );
}
