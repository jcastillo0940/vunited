import CTAButton from '@/components/common/CTAButton';

export default function PlayerHero({ player }) {
    return (
        <section className="relative flex min-h-[60vh] items-end overflow-hidden bg-primary">
            <div className="absolute inset-0 z-0">
                <img
                    alt={player.name}
                    className="h-full w-full object-cover object-top opacity-80"
                    src={player.imageUrl}
                />
                <div className="absolute inset-0 bg-[linear-gradient(rgba(29,66,138,0.8),rgba(29,66,138,0.95))]" />
            </div>
            <div className="relative z-10 mx-auto w-full max-w-7xl px-margin-mobile pb-12 md:px-margin-desktop">
                <div className="flex flex-col gap-8 md:flex-row md:items-end md:justify-between">
                    <div className="space-y-2">
                        <span className="inline-block rounded-sm bg-accent px-4 py-1 font-display text-lg font-bold uppercase text-white">
                            {player.position}
                        </span>
                        <h1 className="font-display text-6xl font-black uppercase leading-none tracking-tight text-white md:text-8xl">
                            {player.firstName}
                            <br />
                            {player.lastName}
                        </h1>
                        <div className="flex items-center gap-4 font-display text-2xl text-white/70">
                            <span className="font-bold">{player.number}</span>
                            <span className="h-[2px] w-12 bg-accent/50" />
                            <span className="font-medium">{player.profile.team}</span>
                        </div>
                    </div>

                    <div className="flex flex-wrap gap-4">
                        <CTAButton variant="primary" size="md" className="gap-2">
                            <span className="material-symbols-outlined text-base">person_add</span>
                            Recibir Actualizaciones
                        </CTAButton>
                        <CTAButton variant="ghost" size="md" className="gap-2 border-2 border-white">
                            <span className="material-symbols-outlined text-base">mail</span>
                            Contactar Representante
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
