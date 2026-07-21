import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Build estatico servido por nginx en la raiz del dominio publico dedicado
// (boletos.wp-pa.com) - el SPA vive en "/", no bajo un prefijo /builds/.
export default defineConfig({
    plugins: [react()],
    base: '/',
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    },
    server: {
        port: 5175,
    },
});
