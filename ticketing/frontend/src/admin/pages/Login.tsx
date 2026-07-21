import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Logo, Alert } from '@veraguas/ui';
import { login, storeToken } from '../../api/auth';
import { ApiError } from '../../api/client';

export function Login() {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSubmitting(true);
        setError(null);
        try {
            const res = await login(email, password, 'backoffice');
            if (res.operator.role !== 'admin') {
                setError('Esta cuenta no tiene permisos de administrador.');

                return;
            }
            storeToken(res.token);
            navigate('/admin');
        } catch (err) {
            setError(err instanceof ApiError ? 'Credenciales inválidas.' : 'No se pudo conectar.');
        } finally {
            setSubmitting(false);
        }
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
                            <Input id="email" type="email" autoComplete="username" required value={email} onChange={(e) => setEmail(e.target.value)} />
                        </FormField>
                        <FormField htmlFor="password" label="Contraseña" required>
                            <Input id="password" type="password" autoComplete="current-password" required value={password} onChange={(e) => setPassword(e.target.value)} />
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
