import FileUploadBox from '@/components/forms/FileUploadBox';

export default function MemberPhotoUpload() {
    return (
        <section>
            <SectionHeader number="02." title="Fotografia del Socio" />
            <FileUploadBox
                label=""
                helper="PNG o JPG (Max. 5MB). Fondo liso recomendado. Visual solamente, sin subida real."
            />
        </section>
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
