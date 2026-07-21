import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Logo, Alert } from '@veraguas/ui';

export function Login() {
    const navigate = useNavigate();
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSubmitting(true);
        setError(null);
        // Shell: la autenticacion real contra el backend de Web llega en otra fase.
        window.setTimeout(() => {
            setSubmitting(false);
            navigate('/admin');
        }, 400);
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-primary px-4">
            <Container className="max-w-sm">
                <div className="mb-8 flex flex-col items-center gap-3 text-white">
                    <Logo className="h-14 w-14" />
                    <p className="font-display text-lg font-bold uppercase tracking-tight">Panel administrativo</p>
                </div>
                <Card>
                    <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                        {error ? <Alert tone="danger">{error}</Alert> : null}
                        <FormField htmlFor="email" label="Correo" required>
                            <Input id="email" name="email" type="email" autoComplete="username" required />
                        </FormField>
                        <FormField htmlFor="password" label="Contraseña" required>
                            <Input id="password" name="password" type="password" autoComplete="current-password" required />
                        </FormField>
                        <Button type="submit" size="lg" pending={submitting} pendingLabel="Ingresando…">
                            Ingresar
                        </Button>
                    </form>
                </Card>
            </Container>
        </div>
    );
}
