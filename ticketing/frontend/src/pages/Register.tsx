import { useState, type FormEvent } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Alert } from '@veraguas/ui';
import { useCustomerAuth } from '../context/CustomerAuthContext';
import { ApiError } from '../api/client';

export function Register() {
    const { register } = useCustomerAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const from = (location.state as { from?: string } | null)?.from ?? '/';

    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSubmitting(true);
        setError(null);
        try {
            await register(name, email, password);
            navigate(from, { replace: true });
        } catch (err) {
            setError(err instanceof ApiError ? 'No se pudo crear tu cuenta. Revisa los datos.' : 'No se pudo conectar.');
        } finally {
            setSubmitting(false);
        }
    }

    return (
        <Container className="section-space max-w-sm">
            <h1 className="section-heading mb-8">Crear cuenta</h1>
            <Card>
                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    {error ? <Alert tone="danger">{error}</Alert> : null}
                    <FormField htmlFor="name" label="Nombre completo" required>
                        <Input id="name" required value={name} onChange={(e) => setName(e.target.value)} />
                    </FormField>
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
                            autoComplete="new-password"
                            required
                            minLength={8}
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                        />
                    </FormField>
                    <Button type="submit" size="lg" pending={submitting} pendingLabel="Creando cuenta…">
                        Crear cuenta
                    </Button>
                </form>
            </Card>
            <p className="mt-6 text-center text-sm text-text-main/70">
                ¿Ya tienes cuenta?{' '}
                <Link to="/ingresar" className="font-semibold text-primary">
                    Ingresa aquí
                </Link>
            </p>
        </Container>
    );
}
