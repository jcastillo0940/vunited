import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from './layouts/RouteShell';
import { Catalog } from './pages/Catalog';
import { Category } from './pages/Category';
import { Product } from './pages/Product';
import { Cart } from './pages/Cart';
import { Checkout } from './pages/Checkout';
import { Confirmation } from './pages/Confirmation';
import { OrderLookup } from './pages/OrderLookup';
import { PaymentError } from './pages/PaymentError';
import { PaymentPending } from './pages/PaymentPending';
import { NotFound } from './pages/NotFound';
import { ErrorBoundary } from './ErrorBoundary';

export function App() {
    return (
        <ErrorBoundary>
            <BrowserRouter>
                <Routes>
                    <Route element={<RouteShell />}>
                        <Route path="/" element={<Catalog />} />
                        <Route path="/categoria/:slug" element={<Category />} />
                        <Route path="/producto/:slug" element={<Product />} />
                        <Route path="/carrito" element={<Cart />} />
                        <Route path="/checkout" element={<Checkout />} />
                        <Route path="/confirmacion" element={<Confirmation />} />
                        <Route path="/orden" element={<OrderLookup />} />
                        <Route path="/pago/error" element={<PaymentError />} />
                        <Route path="/pago/pendiente" element={<PaymentPending />} />
                        <Route path="*" element={<NotFound />} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </ErrorBoundary>
    );
}
