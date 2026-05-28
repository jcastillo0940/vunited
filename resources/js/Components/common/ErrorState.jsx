import CTAButton from '@/components/common/CTAButton';

export default function ErrorState({
    title = 'No se pudo cargar este bloque',
    description = 'Intenta nuevamente en unos momentos.',
    action,
}) {
    return (
        <div className="surface-panel p-8 text-center">
            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-600">
                <span className="material-symbols-outlined">error</span>
            </div>
            <h3 className="mt-4 font-display text-2xl font-bold uppercase text-primary">
                {title}
            </h3>
            <p className="mt-3 text-sm leading-relaxed text-gray-600">
                {description}
            </p>
            {action ? (
                <div className="mt-6">
                    <CTAButton onClick={action.onClick} variant="secondary">
                        {action.label}
                    </CTAButton>
                </div>
            ) : null}
        </div>
    );
}
