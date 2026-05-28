import CTAButton from '@/components/common/CTAButton';

export default function RegistrationSummary({ summary, loading = false, error = null }) {
    return (
        <aside className="lg:col-span-1">
            <div className="sticky top-44 overflow-hidden rounded-xl border border-outline bg-white p-10 shadow-xl">
                <div className="absolute left-0 top-0 h-2 w-full bg-primary" />
                <h3 className="mb-10 border-b border-outline pb-5 font-display text-2xl font-bold uppercase text-primary">
                    Resumen de Registro
                </h3>

                <div className="mb-10 space-y-8">
                    <SummaryRow label="Membresía" value={summary.membership} display="headline" />
                    <SummaryRow label="Duración" value={summary.duration} />
                    <SummaryRow label="Acceso Estadio" value={summary.access} />

                    <div className="border-t border-outline pt-8">
                        <div className="flex items-end justify-between">
                            <span className="font-display text-xl font-bold uppercase text-primary">
                                Total a Pagar
                            </span>
                            <div className="text-right">
                                <span className="font-display text-4xl font-bold text-primary">
                                    {summary.total}
                                </span>
                                <p className="mt-1 text-[10px] font-bold uppercase text-on-surface-variant">
                                    {summary.note}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <CTAButton
                    type="submit"
                    variant="primary"
                    size="lg"
                    className="w-full justify-center font-display text-xl tracking-[0.18em]"
                    disabled={loading}
                >
                    {loading ? (
                        <>
                            <span className="material-symbols-outlined animate-spin text-base">
                                autorenew
                            </span>
                            PROCESANDO...
                        </>
                    ) : (
                        <>
                            <span className="material-symbols-outlined text-base">payments</span>
                            PAGAR CON PAYPAL
                        </>
                    )}
                </CTAButton>

                {error ? (
                    <div className="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                        {error}
                    </div>
                ) : (
                    <p className="mt-4 text-center text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">
                        Serás redirigido a PayPal para completar el pago.
                    </p>
                )}

                <p className="mt-8 text-center text-[11px] font-medium uppercase leading-relaxed tracking-wide text-on-surface-variant">
                    Al completar el registro, aceptas los términos y condiciones del club.
                </p>
            </div>
        </aside>
    );
}

function SummaryRow({ label, value, display = 'body' }) {
    return (
        <div className="flex items-center justify-between">
            <span className="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">
                {label}
            </span>
            <span
                className={
                    display === 'headline'
                        ? 'font-display text-lg font-bold text-primary'
                        : 'text-text-main'
                }
            >
                {value}
            </span>
        </div>
    );
}
