import { NavLink } from 'react-router-dom';
import { Icon, Logo, cx } from '@veraguas/ui';

const ITEMS = [
    { to: '/admin', label: 'Dashboard', icon: 'space_dashboard', end: true },
    { to: '/admin/eventos', label: 'Eventos', icon: 'event' },
    { to: '/admin/ordenes', label: 'Órdenes', icon: 'receipt_long' },
    { to: '/admin/operadores', label: 'Operadores', icon: 'group' },
    { to: '/admin/dispositivos', label: 'Dispositivos', icon: 'smartphone' },
    { to: '/admin/validaciones', label: 'Validaciones', icon: 'history' },
];

export function Sidebar() {
    return (
        <aside className="hidden w-64 shrink-0 flex-col border-r border-outline bg-primary text-white md:flex">
            <div className="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <Logo className="h-9 w-9 text-white" />
                <span className="font-display text-sm font-bold uppercase tracking-tight">Boletería Admin</span>
            </div>
            <nav aria-label="Navegación administrativa" className="flex flex-1 flex-col gap-1 p-4">
                {ITEMS.map((item) => (
                    <NavLink
                        key={item.to}
                        to={item.to}
                        end={item.end}
                        className={({ isActive }) =>
                            cx(
                                'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold uppercase tracking-tight transition-colors',
                                isActive ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white',
                            )
                        }
                    >
                        <Icon name={item.icon} size="sm" />
                        {item.label}
                    </NavLink>
                ))}
            </nav>
        </aside>
    );
}
