import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Build estatico servido por nginx (adaptado de Apache en Fase 2) desde
// /var/www/veraguas-web/builds/. No se usa servidor de desarrollo en produccion.
export default defineConfig({
    plugins: [react()],
    base: '/builds/',
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    },
    server: {
        port: 5173,
    },
});
