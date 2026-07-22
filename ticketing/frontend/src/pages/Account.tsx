import { useNavigate } from 'react-router-dom';
import { Container, Card, Button } from '@veraguas/ui';
import { useCustomerAuth } from '../context/CustomerAuthContext';

export function Account() {
    const { customer, logout } = useCustomerAuth();
    const navigate = useNavigate();

    async function handleLogout() {
        await logout();
        navigate('/');
    }

    if (!customer) return null;

    return (
        <Container className="section-space max-w-lg">
            <h1 className="section-heading mb-8">Mi cuenta</h1>
            <Card className="flex flex-col gap-2">
                <p className="text-sm text-text-main/60">Nombre</p>
                <p className="font-semibold text-text-main">{customer.name}</p>
                <p className="mt-4 text-sm text-text-main/60">Correo</p>
                <p className="font-semibold text-text-main">{customer.email}</p>
            </Card>
            <div className="mt-6 flex flex-col gap-3">
                <Button as="a" href="/historial" variant="secondary" className="w-full">
                    Ver historial de compras
                </Button>
                <Button as="a" href="/wallet" variant="outline" className="w-full">
                    Mi wallet
                </Button>
                <Button onClick={handleLogout} variant="outline" className="w-full">
                    Cerrar sesión
                </Button>
            </div>
        </Container>
    );
}
