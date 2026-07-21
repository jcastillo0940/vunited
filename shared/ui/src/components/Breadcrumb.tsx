import { Icon } from './Icon';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

export interface BreadcrumbProps {
    items: BreadcrumbItem[];
}

export function Breadcrumb({ items }: BreadcrumbProps) {
    return (
        <nav aria-label="Ruta de navegación">
            <ol className="flex flex-wrap items-center gap-1 text-xs text-text-main/60">
                {items.map((item, index) => {
                    const isLast = index === items.length - 1;
                    return (
                        <li key={item.label} className="flex items-center gap-1">
                            {item.href && !isLast ? (
                                <a href={item.href} className="hover:text-primary hover:underline">
                                    {item.label}
                                </a>
                            ) : (
                                <span aria-current={isLast ? 'page' : undefined} className={isLast ? 'font-semibold text-primary' : ''}>
                                    {item.label}
                                </span>
                            )}
                            {!isLast ? <Icon name="chevron_right" size="sm" /> : null}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
