import { createContext, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';
import {
    type CustomerView,
    clearCustomerToken,
    getMyProfile,
    getStoredCustomerToken,
    loginCustomer,
    logoutCustomer,
    registerCustomer,
    storeCustomerToken,
} from '../api/customer';

interface CustomerAuthContextValue {
    customer: CustomerView | null;
    loading: boolean;
    login: (email: string, password: string) => Promise<void>;
    register: (name: string, email: string, password: string) => Promise<void>;
    logout: () => Promise<void>;
}

const CustomerAuthContext = createContext<CustomerAuthContextValue | null>(null);

export function CustomerAuthProvider({ children }: { children: ReactNode }) {
    const [customer, setCustomer] = useState<CustomerView | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!getStoredCustomerToken()) {
            setLoading(false);

            return;
        }
        getMyProfile()
            .then((res) => setCustomer(res.customer))
            .catch(() => clearCustomerToken())
            .finally(() => setLoading(false));
    }, []);

    const value = useMemo<CustomerAuthContextValue>(
        () => ({
            customer,
            loading,
            async login(email, password) {
                const res = await loginCustomer(email, password);
                storeCustomerToken(res.token);
                setCustomer(res.customer);
            },
            async register(name, email, password) {
                const res = await registerCustomer(name, email, password);
                storeCustomerToken(res.token);
                setCustomer(res.customer);
            },
            async logout() {
                try {
                    await logoutCustomer();
                } finally {
                    clearCustomerToken();
                    setCustomer(null);
                }
            },
        }),
        [customer, loading],
    );

    return <CustomerAuthContext.Provider value={value}>{children}</CustomerAuthContext.Provider>;
}

export function useCustomerAuth(): CustomerAuthContextValue {
    const ctx = useContext(CustomerAuthContext);
    if (!ctx) throw new Error('useCustomerAuth debe usarse dentro de <CustomerAuthProvider>');

    return ctx;
}
