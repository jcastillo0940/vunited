import { useEffect, useRef, type ReactNode } from 'react';
import { Icon } from './Icon';
import { zIndex } from '../tokens';

export interface ModalProps {
    open: boolean;
    onClose: () => void;
    title: string;
    children: ReactNode;
}

/** Dialogo modal accesible: foco atrapado por el navegador vía <dialog>, Esc cierra. */
export function Modal({ open, onClose, title, children }: ModalProps) {
    const ref = useRef<HTMLDialogElement>(null);

    useEffect(() => {
        const dialog = ref.current;
        if (!dialog) return;
        if (open && !dialog.open) dialog.showModal();
        if (!open && dialog.open) dialog.close();
    }, [open]);

    return (
        <dialog
            ref={ref}
            onClose={onClose}
            aria-labelledby="modal-title"
            className="w-full max-w-lg rounded-xl border border-outline p-0 shadow-panel backdrop:bg-primary/40"
            style={{ zIndex: zIndex.modal }}
        >
            <div className="flex items-center justify-between border-b border-outline px-6 py-4">
                <h2 id="modal-title" className="font-display text-lg font-bold uppercase text-primary">
                    {title}
                </h2>
                <button
                    type="button"
                    onClick={onClose}
                    aria-label="Cerrar"
                    className="rounded-md p-1 text-text-main/60 hover:bg-surface hover:text-text-main"
                >
                    <Icon name="close" size="sm" />
                </button>
            </div>
            <div className="p-6">{children}</div>
        </dialog>
    );
}
