export interface FieldErrorProps {
    children?: string | null;
    id?: string;
}

export function FieldError({ children, id }: FieldErrorProps) {
    if (!children) return null;
    return (
        <p id={id} role="alert" className="mt-1 text-xs font-medium text-red-600">
            {children}
        </p>
    );
}
