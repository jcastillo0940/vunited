import '@testing-library/jest-dom/vitest';

// jsdom no implementa <dialog>.showModal()/close() (usado por Modal.tsx).
// Polyfill mínimo solo para pruebas — el comportamiento real lo da el navegador.
if (typeof HTMLDialogElement !== 'undefined') {
    if (!HTMLDialogElement.prototype.showModal) {
        HTMLDialogElement.prototype.showModal = function showModal(this: HTMLDialogElement) {
            this.setAttribute('open', '');
        };
    }
    if (!HTMLDialogElement.prototype.close) {
        HTMLDialogElement.prototype.close = function close(this: HTMLDialogElement) {
            this.removeAttribute('open');
        };
    }
}
