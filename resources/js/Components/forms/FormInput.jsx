export default function FormInput({
    label,
    type = 'text',
    name,
    options = [],
    textarea = false,
    className = '',
    ...props
}) {
    const fieldClass = [
        'w-full rounded-md border border-outline bg-surface px-4 py-3 text-sm text-text-main outline-none transition-all placeholder:text-gray-400 focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20',
        className,
    ].join(' ');

    return (
        <label className="block space-y-2">
            {label ? (
                <span className="text-[10px] font-bold uppercase tracking-athletic text-primary">
                    {label}
                </span>
            ) : null}
            {textarea ? <textarea name={name} className={fieldClass} {...props} /> : null}
            {!textarea && type === 'select' ? (
                <select name={name} className={fieldClass} {...props}>
                    {options.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            ) : null}
            {!textarea && type !== 'select' ? (
                <input type={type} name={name} className={fieldClass} {...props} />
            ) : null}
        </label>
    );
}
