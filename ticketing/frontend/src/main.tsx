import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { App } from './App';
import './styles/index.css';

const rootElement = document.getElementById('root');
if (!rootElement) {
    throw new Error('No se encontró el elemento #root');
}

createRoot(rootElement).render(
    <StrictMode>
        <App />
    </StrictMode>,
);

// Service worker del escaner: permite que /escaner cargue sin red. Las
// llamadas a la API nunca se sirven desde cache (ver public/sw.js).
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => null);
    });
}
