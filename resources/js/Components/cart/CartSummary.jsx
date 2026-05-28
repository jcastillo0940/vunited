import CTAButton from '@/components/common/CTAButton';
import CouponBox from '@/components/cart/CouponBox';

export default function CartSummary({
    subtotal,
    shipping,
    discount,
    total,
    couponCode,
    onCouponCodeChange,
    onApplyCoupon,
    couponMessage,
    securityNotice,
    customerName,
    onCustomerNameChange,
    customerEmail,
    onCustomerEmailChange,
    customerPhone,
    onCustomerPhoneChange,
    acceptTerms,
    onAcceptTermsChange,
    onCheckout,
    checkoutLoading,
    checkoutError,
    checkoutDisabled,
}) {
    return (
        <aside className="rounded-xl border border-slate-200 bg-white p-8 shadow-xl lg:sticky lg:top-40">
            <h2 className="border-b border-slate-100 pb-4 font-display text-2xl font-bold uppercase text-primary">
                Resumen
            </h2>

            <div className="mb-8 mt-6 space-y-4">
                <div className="flex justify-between text-slate-600">
                    <span className="font-medium">Subtotal</span>
                    <span className="font-bold text-text-main">${subtotal.toFixed(2)}</span>
                </div>
                <div className="flex justify-between text-slate-600">
                    <span className="font-medium">Envío</span>
                    <span className="font-bold text-text-main">${shipping.toFixed(2)}</span>
                </div>
                <div className="flex justify-between text-slate-600">
                    <span className="font-medium">Descuento</span>
                    <span className="font-bold text-text-main">-${discount.toFixed(2)}</span>
                </div>
                <div className="flex items-center justify-between border-t border-slate-100 pt-4">
                    <span className="font-display text-2xl font-bold uppercase text-primary">
                        Total
                    </span>
                    <span className="font-display text-3xl font-bold text-primary">
                        ${total.toFixed(2)}
                    </span>
                </div>
            </div>

            <CouponBox
                couponCode={couponCode}
                onCouponCodeChange={onCouponCodeChange}
                onApplyCoupon={onApplyCoupon}
                couponMessage={couponMessage}
            />

            <div className="mt-6 space-y-4">
                <div>
                    <label className="mb-2 block text-xs font-bold uppercase tracking-[0.24em] text-slate-400">
                        Nombre completo
                    </label>
                    <input
                        type="text"
                        value={customerName}
                        onChange={(event) => onCustomerNameChange(event.target.value)}
                        className="w-full rounded-md border border-slate-200 px-4 py-3 text-sm text-text-main outline-none transition focus:border-accent"
                        placeholder="Nombre del comprador"
                    />
                </div>
                <div>
                    <label className="mb-2 block text-xs font-bold uppercase tracking-[0.24em] text-slate-400">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        value={customerEmail}
                        onChange={(event) => onCustomerEmailChange(event.target.value)}
                        className="w-full rounded-md border border-slate-200 px-4 py-3 text-sm text-text-main outline-none transition focus:border-accent"
                        placeholder="correo@ejemplo.com"
                    />
                </div>
                <div>
                    <label className="mb-2 block text-xs font-bold uppercase tracking-[0.24em] text-slate-400">
                        Teléfono
                    </label>
                    <input
                        type="text"
                        value={customerPhone}
                        onChange={(event) => onCustomerPhoneChange(event.target.value)}
                        className="w-full rounded-md border border-slate-200 px-4 py-3 text-sm text-text-main outline-none transition focus:border-accent"
                        placeholder="+507 6000-0000"
                    />
                </div>
                <label className="flex items-start gap-3 rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        checked={acceptTerms}
                        onChange={(event) => onAcceptTermsChange(event.target.checked)}
                        className="mt-1"
                    />
                    <span>Acepto crear una orden de tienda y continuar el pago en PayPal. No ingresamos tarjeta ni CVV en esta web.</span>
                </label>
            </div>

            <CTAButton
                variant="primary"
                className="mt-6 w-full font-display text-xl"
                onClick={onCheckout}
                disabled={checkoutDisabled || checkoutLoading}
            >
                {checkoutLoading ? 'REDIRIGIENDO...' : 'CONTINUAR AL PAGO'}
            </CTAButton>

            {checkoutError ? (
                <p className="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {checkoutError}
                </p>
            ) : null}

            <p className="mt-4 text-center text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                El cobro se realiza en PayPal. No almacenamos datos de tarjeta.
            </p>

            <div className="mt-8 border-t border-slate-100 pt-8">
                <div className="flex flex-col items-center gap-4 opacity-70">
                    <p className="text-[10px] font-bold uppercase tracking-[0.28em] text-slate-400">
                        {securityNotice.title}
                    </p>
                    <div className="flex justify-center gap-6 text-slate-400">
                        <span className="material-symbols-outlined text-3xl">credit_card</span>
                        <span className="material-symbols-outlined text-3xl">contactless</span>
                        <span className="material-symbols-outlined text-3xl">verified_user</span>
                    </div>
                    <p className="text-center text-sm leading-6 text-slate-500">
                        {securityNotice.description}
                    </p>
                </div>
            </div>
        </aside>
    );
}
