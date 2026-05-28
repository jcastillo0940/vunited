export default function SectionTitle({
    title,
    eyebrow,
    action,
    align = 'between',
}) {
    const wrapperClass =
        align === 'stacked'
            ? 'space-y-3'
            : 'flex flex-col gap-4 border-b border-gray-200 pb-6 md:flex-row md:items-end md:justify-between';

    return (
        <div className={wrapperClass}>
            <div className="space-y-2">
                {eyebrow ? <p className="display-kicker">{eyebrow}</p> : null}
                <h2 className="section-heading">{title}</h2>
            </div>
            {action ? <div>{action}</div> : null}
        </div>
    );
}
