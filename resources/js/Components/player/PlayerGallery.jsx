export default function PlayerGallery({ gallery }) {
    return (
        <section className="border-y border-gray-200 bg-surface py-24">
            <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                <div className="mb-12 flex items-center justify-between">
                    <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary">
                        Galeria Multimedia
                    </h2>
                    <button
                        type="button"
                        className="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                    >
                        Ver Todo
                        <span className="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </div>

                <div className="grid grid-cols-2 gap-6 md:grid-cols-4">
                    {gallery.map((item) => (
                        <div
                            key={item.id}
                            className="group relative aspect-square cursor-pointer overflow-hidden rounded-xl border border-gray-200 shadow-md"
                        >
                            <img
                                alt={item.label}
                                className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                src={item.imageUrl}
                            />
                            <div className="absolute inset-0 flex items-center justify-center bg-primary/20 opacity-0 transition-opacity group-hover:opacity-100">
                                <span className="material-symbols-outlined text-4xl text-white">
                                    {item.type === 'video' ? 'play_circle' : 'zoom_in'}
                                </span>
                            </div>
                            {item.type === 'video' ? (
                                <div className="absolute bottom-3 left-3 rounded-sm bg-accent px-3 py-1 text-[10px] font-bold text-white">
                                    VIDEO
                                </div>
                            ) : null}
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
