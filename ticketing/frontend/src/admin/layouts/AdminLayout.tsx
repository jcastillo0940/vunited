import type { ReactNode } from 'react';
import { SkipLink, Icon } from '@veraguas/ui';
import { Sidebar } from '../components/Sidebar';

export interface AdminLayoutProps {
    title: string;
    children: ReactNode;
}

export function AdminLayout({ title, children }: AdminLayoutProps) {
    return (
        <div className="flex min-h-screen bg-surface">
            <SkipLink targetId="admin-content" />
            <Sidebar />
            <div className="flex flex-1 flex-col">
                <header className="flex items-center justify-between border-b border-outline bg-white px-6 py-4">
                    <h1 className="font-display text-xl font-bold uppercase text-primary">{title}</h1>
                    <div className="flex items-center gap-2 text-sm text-text-main/70">
                        <Icon name="account_circle" size="sm" />
                        Operador
                    </div>
                </header>
                <main id="admin-content" className="flex-1 p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
