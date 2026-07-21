import { Icon } from '@veraguas/ui';

export interface AdminHeaderProps {
    title: string;
    userName?: string;
}

export function AdminHeader({ title, userName = 'Operador' }: AdminHeaderProps) {
    return (
        <header className="flex items-center justify-between border-b border-outline bg-white px-6 py-4">
            <h1 className="font-display text-xl font-bold uppercase text-primary">{title}</h1>
            <div className="flex items-center gap-2 text-sm text-text-main/70">
                <Icon name="account_circle" size="sm" />
                {userName}
            </div>
        </header>
    );
}
