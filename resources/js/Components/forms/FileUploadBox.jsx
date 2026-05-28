export default function FileUploadBox({
    label = 'Subir archivo',
    helper = 'Arrastra una imagen o selecciona un archivo',
}) {
    return (
        <div className="space-y-2">
            <span className="text-[10px] font-bold uppercase tracking-athletic text-primary">
                {label}
            </span>
            <div className="rounded-xl border-2 border-dashed border-outline bg-surface p-12 text-center transition-all hover:border-accent hover:bg-accent/5">
                <span className="material-symbols-outlined text-5xl text-accent">
                    add_a_photo
                </span>
                <p className="mt-4 font-display text-2xl font-bold uppercase text-primary">
                    Cargar imagen
                </p>
                <p className="mt-2 text-sm text-gray-600">{helper}</p>
            </div>
        </div>
    );
}
