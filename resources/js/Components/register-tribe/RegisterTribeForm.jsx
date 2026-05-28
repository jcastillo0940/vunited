import { useState } from 'react';
import FormInput from '@/components/forms/FormInput';
import MemberPhotoUpload from '@/components/register-tribe/MemberPhotoUpload';
import RegistrationSummary from '@/components/register-tribe/RegistrationSummary';
import membershipService from '@/services/membershipService';

const initialForm = {
    fullName: '',
    email: '',
    nationalId: '',
    birthDate: '',
    age: '',
    address: '',
    phone: '',
    acceptTerms: false,
};

export default function RegisterTribeForm({ summary, membershipPlanCode = 'tribu' }) {
    const [formData, setFormData] = useState(initialForm);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    function handleChange(event) {
        const { name, value, type, checked } = event.target;
        setFormData((current) => ({
            ...current,
            [name]: type === 'checkbox' ? checked : value,
        }));
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const response = await membershipService.createOrder({
                full_name:             formData.fullName,
                email:                 formData.email,
                identification_number: formData.nationalId || null,
                birth_date:            formData.birthDate || null,
                age:                   formData.age ? parseInt(formData.age, 10) : null,
                address:               formData.address || null,
                phone:                 formData.phone || null,
                membership_plan:       membershipPlanCode,
                accept_terms:          formData.acceptTerms ? 1 : 0,
            });

            if (response.data?.approve_url) {
                window.location.href = response.data.approve_url;
            }
        } catch (err) {
            setError(
                err.response?.data?.message ||
                'Error al procesar el registro. Por favor intenta nuevamente.',
            );
        } finally {
            setLoading(false);
        }
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="mx-auto grid max-w-7xl grid-cols-1 gap-16 px-margin-mobile md:px-margin-desktop lg:grid-cols-3"
        >
            <div className="space-y-16 lg:col-span-2">
                <section>
                    <SectionHeader number="01." title="Datos Personales" />
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div className="md:col-span-2">
                            <FormInput
                                label="Nombre Completo"
                                name="fullName"
                                placeholder="Ej. Juan Perez"
                                value={formData.fullName}
                                onChange={handleChange}
                                required
                            />
                        </div>
                        <div className="md:col-span-2">
                            <FormInput
                                label="Correo Electrónico"
                                name="email"
                                type="email"
                                placeholder="Ej. juan@correo.com"
                                value={formData.email}
                                onChange={handleChange}
                                required
                            />
                        </div>
                        <FormInput
                            label="ID / Cédula"
                            name="nationalId"
                            placeholder="9-000-0000"
                            value={formData.nationalId}
                            onChange={handleChange}
                        />
                        <FormInput
                            label="Teléfono"
                            name="phone"
                            type="tel"
                            placeholder="+507 6000-0000"
                            value={formData.phone}
                            onChange={handleChange}
                        />
                        <FormInput
                            label="Fecha de Nacimiento"
                            name="birthDate"
                            type="date"
                            value={formData.birthDate}
                            onChange={handleChange}
                        />
                        <FormInput
                            label="Edad"
                            name="age"
                            type="number"
                            placeholder="00"
                            value={formData.age}
                            onChange={handleChange}
                        />
                        <div className="md:col-span-2">
                            <FormInput
                                label="Dirección de Residencia"
                                name="address"
                                placeholder="Provincia, Distrito, Corregimiento..."
                                value={formData.address}
                                onChange={handleChange}
                            />
                        </div>
                    </div>
                </section>

                <MemberPhotoUpload />

                <section>
                    <SectionHeader number="03." title="Pago" />

                    <div className="rounded-xl border border-outline bg-surface p-8 shadow-sm">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-[#003087] text-white">
                                <span className="material-symbols-outlined">payments</span>
                            </div>
                            <div>
                                <p className="font-display text-xl font-bold uppercase text-primary">
                                    PayPal
                                </p>
                                <p className="text-sm text-text-main/60">
                                    Pago seguro vía PayPal Sandbox
                                </p>
                            </div>
                        </div>

                        <p className="mt-6 text-sm leading-relaxed text-text-main/70">
                            Al continuar, serás redirigido al sitio de PayPal para completar el pago
                            de forma segura. No se procesan ni almacenan datos bancarios en este sitio.
                        </p>

                        <div className="mt-4 rounded-lg border border-accent/30 bg-accent/10 p-3 text-xs font-medium text-primary">
                            Entorno sandbox activo. No se realizarán cobros reales.
                        </div>
                    </div>

                    <label className="mt-8 flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="acceptTerms"
                            checked={formData.acceptTerms}
                            onChange={handleChange}
                            className="mt-1 h-4 w-4 rounded border-outline accent-primary"
                            required
                        />
                        <span className="text-sm leading-relaxed text-text-main/80">
                            Acepto los términos y condiciones de membresía del Club Veraguas United FC
                            y autorizo el procesamiento de mis datos para la gestión del registro.
                        </span>
                    </label>
                </section>
            </div>

            <RegistrationSummary
                summary={summary}
                loading={loading}
                error={error}
            />
        </form>
    );
}

function SectionHeader({ number, title }) {
    return (
        <div className="mb-10 flex items-center gap-5 border-b-4 border-primary pb-4">
            <span className="font-display text-3xl text-accent">{number}</span>
            <h2 className="font-display text-3xl font-bold uppercase tracking-wide text-primary">
                {title}
            </h2>
        </div>
    );
}
