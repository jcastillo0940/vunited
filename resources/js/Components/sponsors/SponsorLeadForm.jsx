import { useState } from 'react';
import CTAButton from '@/components/common/CTAButton';
import FormInput from '@/components/forms/FormInput';

const initialForm = {
    contactName: '',
    company: '',
    email: '',
    phone: '',
    interestLevel: '',
};

export default function SponsorLeadForm({ formConfig }) {
    const [formData, setFormData] = useState(initialForm);
    const [submitted, setSubmitted] = useState(false);

    function handleChange(event) {
        const { name, value } = event.target;

        setFormData((current) => ({
            ...current,
            [name]: value,
        }));
    }

    function handleSubmit(event) {
        event.preventDefault();
        setSubmitted(true);
        console.log('Sponsor lead form submitted', formData);
    }

    return (
        <section className="section-space">
            <div className="page-shell max-w-7xl">
                <div className="overflow-hidden rounded-[32px] border border-primary/10 bg-white shadow-panel">
                    <div className="h-3 w-full bg-accent" />
                    <div className="grid gap-0 lg:grid-cols-[0.92fr_1.08fr]">
                        <div className="bg-primary px-8 py-12 text-white md:px-12 lg:px-14">
                            <p className="text-[10px] font-bold uppercase tracking-[0.35em] text-accent">
                                Commercial lead
                            </p>
                            <h2 className="mt-5 font-display text-4xl font-bold uppercase leading-none tracking-tight md:text-5xl">
                                {formConfig.title}
                            </h2>
                            <p className="mt-6 max-w-md border-l-2 border-accent/70 pl-5 text-sm leading-relaxed text-white/85">
                                {formConfig.description}
                            </p>

                            <div className="mt-10 space-y-4">
                                <div className="rounded-[24px] border border-white/15 bg-white/10 px-5 py-4 backdrop-blur-sm">
                                    <p className="text-[10px] font-bold uppercase tracking-[0.32em] text-accent">
                                        Activaciones
                                    </p>
                                    <p className="mt-2 text-sm text-white/85">
                                        FanFest, matchday, social media y experiencia de marca.
                                    </p>
                                </div>
                                <div className="rounded-[24px] border border-white/15 bg-white/10 px-5 py-4 backdrop-blur-sm">
                                    <p className="text-[10px] font-bold uppercase tracking-[0.32em] text-accent">
                                        Networking
                                    </p>
                                    <p className="mt-2 text-sm text-white/85">
                                        Comunidad de socios, patrocinadores y lideres regionales.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white px-8 py-12 md:px-12 lg:px-14">
                            <form className="space-y-6" onSubmit={handleSubmit}>
                                <div className="grid gap-6 md:grid-cols-2">
                                    <FormInput
                                        label="Nombre del contacto"
                                        name="contactName"
                                        placeholder="Ej. Juan Perez"
                                        value={formData.contactName}
                                        onChange={handleChange}
                                    />
                                    <FormInput
                                        label="Empresa"
                                        name="company"
                                        placeholder="Nombre de la empresa"
                                        value={formData.company}
                                        onChange={handleChange}
                                    />
                                </div>

                                <div className="grid gap-6 md:grid-cols-2">
                                    <FormInput
                                        label="Correo electronico"
                                        type="email"
                                        name="email"
                                        placeholder="correo@empresa.com"
                                        value={formData.email}
                                        onChange={handleChange}
                                    />
                                    <FormInput
                                        label="Telefono"
                                        type="tel"
                                        name="phone"
                                        placeholder="+507 6000-0000"
                                        value={formData.phone}
                                        onChange={handleChange}
                                    />
                                </div>

                                <FormInput
                                    label="Nivel de interes"
                                    type="select"
                                    name="interestLevel"
                                    options={formConfig.interestOptions}
                                    value={formData.interestLevel}
                                    onChange={handleChange}
                                />

                                <div className="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                                    <CTAButton type="submit" variant="secondary" size="md">
                                        ENVIAR PROPUESTA
                                    </CTAButton>
                                    {submitted ? (
                                        <p className="text-sm font-medium text-primary">
                                            Solicitud simulada enviada. Revisa la consola para ver el payload.
                                        </p>
                                    ) : (
                                        <p className="text-sm text-gray-500">
                                            Complete el formulario y nos pondremos en contacto.
                                        </p>
                                    )}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
