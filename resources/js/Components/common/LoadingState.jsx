export default function LoadingState({ title = 'Cargando contenido' }) {
    return (
        <div className="surface-panel flex min-h-40 items-center justify-center p-8 text-center">
            <div className="space-y-3">
                <div className="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-surface border-t-accent" />
                <p className="font-body text-sm font-semibold uppercase tracking-athletic text-primary">
                    {title}
                </p>
            </div>
        </div>
    );
}
