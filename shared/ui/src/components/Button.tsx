import type { AnchorHTMLAttributes, ButtonHTMLAttributes } from 'react';
import { cx } from '../cx';

type Variant = 'primary' | 'secondary' | 'ghost' | 'outline';
type Size = 'sm' | 'md' | 'lg';

const VARIANT_CLASS: Record<Variant, string> = {
    primary: 'bg-primary text-on-primary hover:bg-primary/90',
    secondary: 'bg-accent text-on-accent hover:bg-accent/90',
    ghost: 'bg-transparent text-primary hover:bg-surface',
    outline: 'border border-outline bg-transparent text-primary hover:bg-surface',
};

const SIZE_CLASS: Record<Size, string> = {
    sm: 'px-4 py-1 text-[10px]',
    md: 'px-6 py-3 text-sm',
    lg: 'px-8 py-4 text-base',
};

interface CommonProps {
    variant?: Variant;
    size?: Size;
    pending?: boolean;
    pendingLabel?: string;
    className?: string;
}

export type ButtonProps = CommonProps & ButtonHTMLAttributes<HTMLButtonElement> & { as?: 'button' };
export type ButtonAsAnchorProps = CommonProps & AnchorHTMLAttributes<HTMLAnchorElement> & { as: 'a' };

const BASE_CLASS =
    'inline-flex items-center justify-center gap-2 rounded-md font-display font-bold uppercase tracking-tight transition-colors disabled:cursor-not-allowed disabled:opacity-60';

/** Equivalente tipado de CTAButton.jsx del frontend actual. */
export function Button(props: ButtonProps | ButtonAsAnchorProps) {
    const { variant = 'primary', size = 'md', pending, pendingLabel, className, children } = props;
    const classes = cx(BASE_CLASS, VARIANT_CLASS[variant], SIZE_CLASS[size], className);
    const content = pending ? (pendingLabel ?? children) : children;

    if (props.as === 'a') {
        const { variant: _v, size: _s, pending: _p, pendingLabel: _pl, className: _c, as: _as, children: _ch, ...anchorRest } = props;
        return (
            <a className={classes} aria-disabled={pending} {...anchorRest}>
                {content}
            </a>
        );
    }

    const { variant: _v, size: _s, pending: _p, pendingLabel: _pl, className: _c, as: _as, children: _ch, ...buttonRest } = props;
    return (
        <button type="button" className={classes} disabled={pending || buttonRest.disabled} {...buttonRest}>
            {content}
        </button>
    );
}
