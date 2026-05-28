export default function CTAButton({
    as: Component = 'button',
    variant = 'primary',
    size = 'md',
    className = '',
    children,
    ...props
}) {
    const variantClasses = {
        primary:
            'bg-accent text-on-accent hover:bg-primary hover:text-white shadow-lg shadow-accent/20',
        secondary:
            'bg-primary text-on-primary hover:bg-accent hover:text-primary shadow-md',
        outline:
            'border-2 border-primary bg-transparent text-primary hover:bg-primary hover:text-white',
        ghost:
            'bg-white/10 text-white hover:bg-white/20',
    };

    const sizeClasses = {
        sm: 'px-4 py-2 text-[10px]',
        md: 'px-6 py-3 text-sm',
        lg: 'px-8 py-4 text-base md:text-2xl',
    };

    const classes = [
        'inline-flex items-center justify-center gap-3 rounded-md font-bold uppercase tracking-wide transition-all duration-200 active:scale-[0.98]',
        sizeClasses[size],
        variantClasses[variant],
        className,
    ].join(' ');

    return (
        <Component className={classes} {...props}>
            {children}
        </Component>
    );
}
