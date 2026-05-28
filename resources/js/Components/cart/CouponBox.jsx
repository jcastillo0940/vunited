export default function CouponBox({
    couponCode,
    onCouponCodeChange,
    onApplyCoupon,
    couponMessage,
}) {
    return (
        <div className="mb-8">
            <label className="mb-3 block text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">
                Codigo de descuento
            </label>
            <div className="flex gap-3">
                <input
                    type="text"
                    value={couponCode}
                    onChange={(event) => onCouponCodeChange(event.target.value)}
                    placeholder="INTRODUCIR CODIGO"
                    className="w-full border-b border-slate-200 bg-transparent px-0 py-2 text-sm font-medium uppercase text-text-main placeholder:text-slate-300 focus:border-accent focus:ring-0"
                />
                <button
                    type="button"
                    onClick={onApplyCoupon}
                    className="text-xs font-bold uppercase text-accent transition hover:text-primary"
                >
                    Aplicar
                </button>
            </div>
            {couponMessage ? (
                <p className="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-accent">
                    {couponMessage}
                </p>
            ) : null}
        </div>
    );
}
