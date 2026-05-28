export default function TicketCheckoutSummary({
    selectedZone,
    quantity,
    total,
    customerEmail,
    onEmailChange,
    termsAccepted,
    onTermsChange,
    onPayNow,
    loading = false,
    error = null,
}) {
    const canPay = !selectedZone.outOfStock && customerEmail && termsAccepted && !loading;

    return (
        <aside className="sticky top-40 rounded-xl bg-primary p-10 text-white shadow-xl">
            <h2 className="mb-8 border-b border-white/10 pb-4 font-display text-2xl font-bold uppercase text-accent">
                Resumen de compra
            </h2>

            <div className="mb-8 space-y-6">
                <div className="flex items-center justify-between">
                    <span className="text-xs font-bold uppercase tracking-[0.24em] text-white/60">
                        Zona seleccionada
                    </span>
                    <span className="font-display text-lg font-bold uppercase">
                        {selectedZone.displayName}
                    </span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="text-xs font-bold uppercase tracking-[0.24em] text-white/60">
                        Precio unitario
                    </span>
                    <span className="font-display text-lg font-bold">
                        ${selectedZone.price.toFixed(2)}
                    </span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="text-xs font-bold uppercase tracking-[0.24em] text-white/60">
                        Cantidad
                    </span>
                    <span className="font-display text-lg font-bold">x{quantity}</span>
                </div>
                <div className="flex items-end justify-between border-t border-white/20 pt-6">
                    <span className="font-display text-3xl font-black uppercase leading-none text-accent">
                        Total
                    </span>
                    <span className="font-display text-5xl font-black leading-none">
                        ${total.toFixed(2)}
                    </span>
                </div>
                {selectedZone.availableQuantity !== null ? (
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-bold uppercase tracking-[0.24em] text-white/60">
                            Disponibilidad
                        </span>
                        <span className="font-display text-lg font-bold">
                            {selectedZone.availableQuantity}
                        </span>
                    </div>
                ) : null}
            </div>

            <div className="mb-6 space-y-4">
                <div>
                    <label className="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.24em] text-white/60">
                        Correo electrónico *
                    </label>
                    <input
                        type="email"
                        value={customerEmail}
                        onChange={(e) => onEmailChange(e.target.value)}
                        placeholder="tu@correo.com"
                        required
                        className="w-full rounded-md border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent"
                    />
                </div>

                <label className="flex cursor-pointer items-start gap-3">
                    <input
                        type="checkbox"
                        checked={termsAccepted}
                        onChange={(e) => onTermsChange(e.target.checked)}
                        className="mt-0.5 h-4 w-4 accent-accent"
                    />
                    <span className="text-xs leading-relaxed text-white/70">
                        Acepto los términos de compra de boletos de Veraguas United FC.
                    </span>
                </label>
            </div>

            {error ? (
                <div className="mb-4 rounded-md border border-red-400/30 bg-red-500/20 p-3 text-xs text-red-200">
                    {error}
                </div>
            ) : null}

            <button
                type="button"
                onClick={onPayNow}
                disabled={!canPay}
                className="w-full rounded-md bg-accent py-6 font-display text-2xl font-black uppercase text-white shadow-lg transition hover:bg-white hover:text-primary active:scale-[0.98] disabled:cursor-not-allowed disabled:bg-white/20 disabled:text-white/60"
            >
                {loading
                    ? 'REDIRIGIENDO...'
                    : selectedZone.outOfStock
                    ? 'AGOTADO'
                    : 'PAGAR CON PAYPAL'}
            </button>

            <p className="mt-6 text-center text-[10px] font-bold uppercase tracking-[0.28em] text-white/40">
                Entorno sandbox activo · Sin cobro real todavía
            </p>
        </aside>
    );
}
