import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Alert } from '@veraguas/ui';
import { useCustomerAuth } from '../context/CustomerAuthContext';
import { ApiError } from '../api/client';

export function Login() {
    const { login } = useCustomerAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const from = (location.state as { from?: string } | null)?.from ?? '/';

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSubmitting(true);
        setError(null);
        try {
            await login(email, password);
            navigate(from, { replace: true });
        } catch (err) {
            setError(err instanceof ApiError ? 'Correo o contraseña incorrectos.' : 'No se pudo conectar.');
        } finally {
            setSubmitting(false);
        }
    }

    return (
        <Container className="section-space max-w-sm">
            <h1 className="section-heading mb-8">Ingresar</h1>
            <Card>
                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    {error ? <Alert tone="danger">{error}</Alert> : null}
                    <FormField htmlFor="email" label="Correo" required>
                        <Input
                            id="email"
                            type="email"
                            autoComplete="username"
                            required
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                        />
                    </FormField>
                    <FormField htmlFor="password" label="Contraseña" required>
                        <Input
                            id="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                        />
                    </FormField>
                    <Button type="submit" size="lg" pending={submitting} pendingLabel="Ingresando…">
                        Ingresar
                    </Button>
                </form>
            </Card>
            <p className="mt-6 text-center text-sm text-text-main/70">
                ¿No tienes cuenta?{' '}
                <Link to="/registro" className="font-semibold text-primary">
                    Crea una aquí
                </Link>
            </p>
        </Container>
    );
}
