// Copiado 1:1 de tailwind.config.js (frontend institucional actual).
// No redefinir valores aquí sin actualizar también tailwind.config.js del
// monolito legado: mientras ambos existan, deben coincidir.
export const colors = {
    primary: '#1D428A',
    accent: '#5BC2E7',
    secondary: '#5BC2E7',
    background: '#FFFFFF',
    surface: '#F4F6F9',
    textMain: '#2B2B2B',
    onPrimary: '#FFFFFF',
    onAccent: '#FFFFFF',
    outline: '#E2E8F0',
    surfaceContainerLow: '#F8FAFC',
    surfaceContainerHigh: '#E2E8F0',
} as const;

export type ColorToken = keyof typeof colors;
