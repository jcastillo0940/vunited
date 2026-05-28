import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#1D428A',
                accent: '#5BC2E7',
                secondary: '#5BC2E7',
                background: '#FFFFFF',
                surface: '#F4F6F9',
                'text-main': '#2B2B2B',
                'on-primary': '#FFFFFF',
                'on-accent': '#FFFFFF',
                outline: '#E2E8F0',
                'surface-container-low': '#F8FAFC',
                'surface-container-high': '#E2E8F0',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Oswald', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                'margin-mobile': '16px',
                'margin-desktop': '32px',
                gutter: '16px',
                unit: '4px',
            },
            borderRadius: {
                md: '0.375rem',
                lg: '0.5rem',
                xl: '0.75rem',
                full: '9999px',
            },
            boxShadow: {
                ticker: '0 1px 2px rgba(15, 23, 42, 0.08)',
                card: '0 10px 30px rgba(15, 23, 42, 0.08)',
                panel: '0 18px 40px rgba(15, 23, 42, 0.12)',
                float: '0 20px 45px rgba(29, 66, 138, 0.16)',
            },
            maxWidth: {
                shell: '80rem',
            },
            letterSpacing: {
                athletic: '0.24em',
            },
            keyframes: {
                shimmer: {
                    '0%':   { transform: 'translateX(-150%) skewX(-12deg)' },
                    '100%': { transform: 'translateX(350%) skewX(-12deg)' },
                },
            },
            animation: {
                shimmer: 'shimmer 2.8s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
