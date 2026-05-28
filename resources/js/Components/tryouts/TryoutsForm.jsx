import { useState } from 'react';
import CTAButton from '@/components/common/CTAButton';
import FileUploadBox from '@/components/forms/FileUploadBox';
import FormInput from '@/components/forms/FormInput';

const initialForm = {
    fullName: '',
    birthDate: '',
    age: '',
    position: '',
    location: '',
    consent: false,
};

export default function TryoutsForm({ positionOptions }) {
    const [formData, setFormData] = useState(initialForm);
    const [submitted, setSubmitted] = useState(false);

    function handleChange(event) {
        const { name, value, type, checked } = event.target;

        setFormData((current) => ({
            ...current,
            [name]: type === 'checkbox' ? checked : value,
        }));
    }

    function handleSubmit(event) {
        event.preventDefault();
        setSubmitted(true);
        console.log('Tryouts form prepared', formData);
    }

    return (
        <section className="bg-surface px-margin-mobile py-20 md:px-margin-desktop">
            <div className="mx-auto max-w-4xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md">
                <div className="bg-primary p-8 text-white md:p-10">
                    <h2 className="font-display text-3xl font-bold uppercase tracking-wide">
                        Formulario de Inscripcion
                    </h2>
                    <p className="mt-2 text-sm text-white/80">
                        Complete todos los campos requeridos para procesar su solicitud de ingreso.
                    </p>
                </div>

                <form className="space-y-10 p-8 md:p-12" onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <FormInput
                            label="Nombre Completo"
                            name="fullName"
                            placeholder="Ej: Juan Perez"
                            value={formData.fullName}
                            onChange={handleChange}
                        />

                        <div className="grid grid-cols-2 gap-4">
                            <FormInput
                                label="Fecha de Nacimiento"
                                type="date"
                                name="birthDate"
                                value={formData.birthDate}
                                onChange={handleChange}
                            />
                            <FormInput
                                label="Edad"
                                type="number"
                                name="age"
                                placeholder="00"
                                value={formData.age}
                                onChange={handleChange}
                            />
                        </div>

                        <FormInput
                            label="Posicion"
                            type="select"
                            name="position"
                            options={positionOptions}
                            value={formData.position}
                            onChange={handleChange}
                        />

                        <FormInput
                            label="Ciudad/Provincia"
                            name="location"
                            placeholder="Ej: Santiago, Veraguas"
                            value={formData.location}
                            onChange={handleChange}
                        />
                    </div>

                    <div className="grid grid-cols-1 gap-8 pt-4 md:grid-cols-2">
                        <FileUploadBox
                            label="Subir Fotos de Accion"
                            helper="Arrastrar archivos o click"
                        />
                        <div className="space-y-2">
                            <FileUploadBox
                                label="Subir Videos/Highlights"
                                helper="Subir video (MP4/MOV)"
                            />
                            <div className="hidden" />
                        </div>
                    </div>

                    <div className="rounded-lg border-l-4 border-accent bg-surface p-6">
                        <label className="flex cursor-pointer items-start gap-4">
                            <input
                                className="mt-1 h-5 w-5 rounded border-gray-300 text-accent focus:ring-accent"
                                type="checkbox"
                                name="consent"
                                checked={formData.consent}
                                onChange={handleChange}
                            />
                            <span className="text-sm leading-relaxed text-text-main">
                                <strong className="mb-1 block text-xs font-bold uppercase">
                                    Consentimiento de los Padres
                                </strong>
                                Confirmo que tengo la autorizacion de mis padres o tutor legal para participar en las pruebas y que me encuentro en condiciones optimas de salud para la practica deportiva de alto rendimiento.
                            </span>
                        </label>
                    </div>

                    <div className="flex justify-center pt-8">
                        <CTAButton type="submit" variant="primary" size="lg" className="w-full gap-4 md:w-auto">
                            ENVIAR SOLICITUD
                            <span className="material-symbols-outlined">send</span>
                        </CTAButton>
                    </div>

                    {submitted ? (
                        <div className="rounded-xl border border-accent/30 bg-accent/10 p-5 text-center text-sm font-medium text-primary">
                            Solicitud preparada localmente. Aun no se envia a backend real en esta fase.
                        </div>
                    ) : null}
                </form>
            </div>
        </section>
    );
}
