import {
    createContext,
    useCallback,
    useContext,
    useMemo,
    useState,
    type ReactNode,
} from 'react';
import { Alert } from './Alert';
import { zIndex } from '../tokens';

export interface ToastItem {
    id: string;
    tone?: 'info' | 'success' | 'warning' | 'danger';
    title?: string;
    message: string;
}

interface ToastContextValue {
    toasts: ToastItem[];
    push: (toast: Omit<ToastItem, 'id'>) => void;
    dismiss: (id: string) => void;
}

const ToastContext = createContext<ToastContextValue | null>(null);

export function ToastProvider({ children }: { children: ReactNode }) {
    const [toasts, setToasts] = useState<ToastItem[]>([]);

    const dismiss = useCallback((id: string) => {
        setToasts((current) => current.filter((toast) => toast.id !== id));
    }, []);

    const push = useCallback((toast: Omit<ToastItem, 'id'>) => {
        const id = crypto.randomUUID();
        setToasts((current) => [...current, { ...toast, id }]);
        window.setTimeout(() => dismiss(id), 5000);
    }, [dismiss]);

    const value = useMemo(() => ({ toasts, push, dismiss }), [toasts, push, dismiss]);

    return (
        <ToastContext.Provider value={value}>
            {children}
            <div
                aria-live="polite"
                aria-atomic="true"
                className="fixed bottom-4 right-4 flex w-80 flex-col gap-2"
                style={{ zIndex: zIndex.toast }}
            >
                {toasts.map((toast) => (
                    <Alert key={toast.id} tone={toast.tone ?? 'info'} title={toast.title}>
                        {toast.message}
                    </Alert>
                ))}
            </div>
        </ToastContext.Provider>
    );
}

export function useToast() {
    const ctx = useContext(ToastContext);
    if (!ctx) throw new Error('useToast debe usarse dentro de <ToastProvider>');
    return ctx;
}
