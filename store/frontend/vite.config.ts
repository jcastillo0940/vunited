import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Build estatico servido por nginx desde /var/www/veraguas-store/builds/.
export default defineConfig({
    plugins: [react()],
    base: '/builds/',
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    },
    server: {
        port: 5174,
    },
});
