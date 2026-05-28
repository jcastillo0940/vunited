export default function AttributeBar({ attribute }) {
    return (
        <div className="space-y-3">
            <div className="flex justify-between text-xs font-bold uppercase text-gray-600">
                <span>{attribute.label}</span>
                <span className="text-primary">{attribute.value}</span>
            </div>
            <div className="h-2.5 overflow-hidden rounded-full bg-gray-200">
                <div
                    className="h-full rounded-full bg-primary"
                    style={{ width: `${attribute.value}%` }}
                />
            </div>
        </div>
    );
}
