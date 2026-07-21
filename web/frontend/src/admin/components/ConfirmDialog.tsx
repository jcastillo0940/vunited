import { Modal, Button } from '@veraguas/ui';

export interface ConfirmDialogProps {
    open: boolean;
    title: string;
    message: string;
    confirmLabel?: string;
    onConfirm: () => void;
    onCancel: () => void;
}

/** Confirmación para acciones destructivas/sensibles del panel admin. */
export function ConfirmDialog({ open, title, message, confirmLabel = 'Confirmar', onConfirm, onCancel }: ConfirmDialogProps) {
    return (
        <Modal open={open} onClose={onCancel} title={title}>
            <p className="text-sm text-text-main/80">{message}</p>
            <div className="mt-6 flex justify-end gap-3">
                <Button variant="outline" onClick={onCancel}>
                    Cancelar
                </Button>
                <Button variant="primary" onClick={onConfirm}>
                    {confirmLabel}
                </Button>
            </div>
        </Modal>
    );
}
