import { useId, type ReactNode } from 'react';
import { cx } from '../cx';

export interface TabItem {
    key: string;
    label: string;
    content: ReactNode;
}

export interface TabsProps {
    items: TabItem[];
    activeKey: string;
    onChange: (key: string) => void;
}

export function Tabs({ items, activeKey, onChange }: TabsProps) {
    const baseId = useId();

    return (
        <div>
            <div role="tablist" className="flex gap-1 border-b border-outline">
                {items.map((item) => {
                    const selected = item.key === activeKey;
                    return (
                        <button
                            key={item.key}
                            role="tab"
                            id={`${baseId}-tab-${item.key}`}
                            aria-selected={selected}
                            aria-controls={`${baseId}-panel-${item.key}`}
                            onClick={() => onChange(item.key)}
                            className={cx(
                                'border-b-2 px-4 py-2 text-sm font-semibold uppercase tracking-tight transition-colors',
                                selected
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-text-main/60 hover:text-primary',
                            )}
                        >
                            {item.label}
                        </button>
                    );
                })}
            </div>
            {items.map((item) => (
                <div
                    key={item.key}
                    role="tabpanel"
                    id={`${baseId}-panel-${item.key}`}
                    aria-labelledby={`${baseId}-tab-${item.key}`}
                    hidden={item.key !== activeKey}
                    className="py-6"
                >
                    {item.key === activeKey ? item.content : null}
                </div>
            ))}
        </div>
    );
}
