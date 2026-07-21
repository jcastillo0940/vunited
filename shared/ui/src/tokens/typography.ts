export const fontFamily = {
    display: ['Oswald', 'sans-serif'],
    body: ['Inter', 'sans-serif'],
} as const;

export const letterSpacing = {
    athletic: '0.24em',
} as const;

// Clases utilitarias replicadas de resources/css/app.css (.display-kicker,
// .section-heading) para que cada frontend nuevo las use tal cual, sin
// reinventar la jerarquia tipografica.
export const typographyPresets = {
    kicker: 'font-body text-[10px] font-bold uppercase tracking-athletic text-accent',
    sectionHeading: 'font-display text-3xl font-bold uppercase tracking-tight text-primary md:text-4xl',
    brandLockup: 'font-display text-2xl font-bold uppercase leading-none tracking-tight',
} as const;
