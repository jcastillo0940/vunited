import type { ReactNode } from 'react';
import { SkipLink } from '@veraguas/ui';
import { Sidebar } from '../components/Sidebar';
import { AdminHeader } from '../components/AdminHeader';

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
                <AdminHeader title={title} />
                <main id="admin-content" className="flex-1 p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
