import veraguasPreset from '../../shared/ui/src/styles/tailwind-preset.mjs';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [veraguasPreset],
    content: [
        './index.html',
        './src/**/*.{ts,tsx}',
        '../../shared/ui/src/**/*.{ts,tsx}',
    ],
    plugins: [forms],
};
