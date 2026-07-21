import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from './layouts/RouteShell';
import { Events } from './pages/Events';
import { MatchDetail } from './pages/MatchDetail';
import { ZoneSelection } from './pages/ZoneSelection';
import { QuantitySelection } from './pages/QuantitySelection';
import { Summary } from './pages/Summary';
import { Checkout } from './pages/Checkout';
import { Confirmation } from './pages/Confirmation';
import { Wallet } from './pages/Wallet';
import { Ticket } from './pages/Ticket';
import { Scanner } from './pages/Scanner';
import { ValidationResult } from './pages/ValidationResult';
import { NotFound } from './pages/NotFound';
import { ErrorBoundary } from './ErrorBoundary';

export function App() {
    return (
        <ErrorBoundary>
            <BrowserRouter>
                <Routes>
                    <Route element={<RouteShell />}>
                        <Route path="/" element={<Events />} />
                        <Route path="/eventos/:id" element={<MatchDetail />} />
                        <Route path="/zona" element={<ZoneSelection />} />
                        <Route path="/cantidad" element={<QuantitySelection />} />
                        <Route path="/resumen" element={<Summary />} />
                        <Route path="/checkout" element={<Checkout />} />
                        <Route path="/confirmacion" element={<Confirmation />} />
                        <Route path="/wallet" element={<Wallet />} />
                        <Route path="/ticket/:id" element={<Ticket />} />
                        <Route path="/escaner" element={<Scanner />} />
                        <Route path="/escaner/resultado" element={<ValidationResult />} />
                        <Route path="*" element={<NotFound />} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </ErrorBoundary>
    );
}
