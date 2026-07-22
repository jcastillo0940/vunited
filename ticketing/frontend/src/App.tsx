import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from './layouts/RouteShell';
import { OrderFlowProvider } from './context/OrderFlowContext';
import { CustomerAuthProvider } from './context/CustomerAuthContext';
import { RequireAuth } from './components/RequireAuth';
import { Events } from './pages/Events';
import { MatchDetail } from './pages/MatchDetail';
import { ZoneSelection } from './pages/ZoneSelection';
import { QuantitySelection } from './pages/QuantitySelection';
import { Summary } from './pages/Summary';
import { Checkout } from './pages/Checkout';
import { Confirmation } from './pages/Confirmation';
import { OrderStatus } from './pages/OrderStatus';
import { OrderLookup } from './pages/OrderLookup';
import { Wallet } from './pages/Wallet';
import { Ticket } from './pages/Ticket';
import { PaymentError } from './pages/PaymentError';
import { PaymentPending } from './pages/PaymentPending';
import { CashPending } from './pages/CashPending';
import { Login } from './pages/Login';
import { Register } from './pages/Register';
import { Account } from './pages/Account';
import { Historial } from './pages/Historial';
import { Scanner } from './pages/Scanner';
import { ScannerLogin } from './pages/ScannerLogin';
import { ValidationResult } from './pages/ValidationResult';
import { NotFound } from './pages/NotFound';
import { ErrorBoundary } from './ErrorBoundary';
import { Login as AdminLogin } from './admin/pages/Login';
import { Dashboard as AdminDashboard } from './admin/pages/Dashboard';
import { Events as AdminEvents } from './admin/pages/Events';
import { Orders as AdminOrders } from './admin/pages/Orders';
import { Operators as AdminOperators } from './admin/pages/Operators';
import { Devices as AdminDevices } from './admin/pages/Devices';
import { Validations as AdminValidations } from './admin/pages/Validations';
import { CashPayments as AdminCashPayments } from './admin/pages/CashPayments';

export function App() {
    return (
        <ErrorBoundary>
            <CustomerAuthProvider>
                <OrderFlowProvider>
                    <BrowserRouter>
                        <Routes>
                        <Route element={<RouteShell />}>
                            <Route path="/" element={<Events />} />
                            <Route path="/eventos/:id" element={<MatchDetail />} />
                            <Route path="/zona" element={<ZoneSelection />} />
                            <Route path="/cantidad" element={<QuantitySelection />} />
                            <Route path="/ingresar" element={<Login />} />
                            <Route path="/registro" element={<Register />} />
                            <Route
                                path="/resumen"
                                element={
                                    <RequireAuth>
                                        <Summary />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/checkout"
                                element={
                                    <RequireAuth>
                                        <Checkout />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/confirmacion"
                                element={
                                    <RequireAuth>
                                        <Confirmation />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/orden"
                                element={
                                    <RequireAuth>
                                        <OrderLookup />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/orden/:orderId"
                                element={
                                    <RequireAuth>
                                        <OrderStatus />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/wallet"
                                element={
                                    <RequireAuth>
                                        <Wallet />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/ticket/:id"
                                element={
                                    <RequireAuth>
                                        <Ticket />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/historial"
                                element={
                                    <RequireAuth>
                                        <Historial />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/cuenta"
                                element={
                                    <RequireAuth>
                                        <Account />
                                    </RequireAuth>
                                }
                            />
                            <Route path="/pago/error" element={<PaymentError />} />
                            <Route
                                path="/pago/pendiente"
                                element={
                                    <RequireAuth>
                                        <PaymentPending />
                                    </RequireAuth>
                                }
                            />
                            <Route
                                path="/pago/efectivo-pendiente"
                                element={
                                    <RequireAuth>
                                        <CashPending />
                                    </RequireAuth>
                                }
                            />
                            <Route path="*" element={<NotFound />} />
                        </Route>
                        <Route path="/escaner/login" element={<ScannerLogin />} />
                        <Route path="/escaner" element={<Scanner />} />
                        <Route path="/escaner/resultado" element={<ValidationResult />} />

                        <Route path="/admin/login" element={<AdminLogin />} />
                        <Route path="/admin" element={<AdminDashboard />} />
                        <Route path="/admin/eventos" element={<AdminEvents />} />
                        <Route path="/admin/ordenes" element={<AdminOrders />} />
                        <Route path="/admin/operadores" element={<AdminOperators />} />
                        <Route path="/admin/dispositivos" element={<AdminDevices />} />
                        <Route path="/admin/validaciones" element={<AdminValidations />} />
                        <Route path="/admin/pagos-efectivo" element={<AdminCashPayments />} />
                        </Routes>
                    </BrowserRouter>
                </OrderFlowProvider>
            </CustomerAuthProvider>
        </ErrorBoundary>
    );
}
