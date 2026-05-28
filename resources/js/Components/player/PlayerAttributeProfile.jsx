import AttributeBar from '@/components/player/AttributeBar';

export default function PlayerAttributeProfile({ attributes }) {
    return (
        <section className="rounded-xl border border-gray-200 bg-surface p-10">
            <h3 className="mb-8 flex items-center gap-3 font-display text-2xl font-bold uppercase text-primary">
                <span className="material-symbols-outlined text-accent">analytics</span>
                Perfil de Atributos
            </h3>
            <div className="grid grid-cols-1 gap-x-16 gap-y-8 md:grid-cols-2">
                {attributes.map((attribute) => (
                    <AttributeBar key={attribute.key} attribute={attribute} />
                ))}
            </div>
        </section>
    );
}
