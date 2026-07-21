import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { RouteShell } from './layouts/RouteShell';
import { Home } from './pages/Home';
import { NewsIndex } from './pages/NewsIndex';
import { NewsShow } from './pages/NewsShow';
import { InstitutionalPage } from './pages/InstitutionalPage';
import { NotFound } from './pages/NotFound';
import { ErrorBoundary } from './ErrorBoundary';
import { Login as AdminLogin } from './admin/pages/Login';
import { Dashboard as AdminDashboard } from './admin/pages/Dashboard';
import { UsersList as AdminUsersList } from './admin/pages/UsersList';
import { AuditLog as AdminAuditLog } from './admin/pages/AuditLog';

const INSTITUTIONAL_PATHS = [
    '/directiva',
    '/plantilla',
    '/fuerzas-basicas',
    '/pruebas',
    '/estadio',
    '/patrocinadores',
    '/fanfest',
    '/expedicion-india',
];

export function App() {
    return (
        <ErrorBoundary>
            <BrowserRouter>
                <Routes>
                    <Route path="/admin/login" element={<AdminLogin />} />
                    <Route path="/admin" element={<AdminDashboard />} />
                    <Route path="/admin/usuarios" element={<AdminUsersList />} />
                    <Route path="/admin/auditoria" element={<AdminAuditLog />} />

                    <Route element={<RouteShell />}>
                        <Route path="/" element={<Home />} />
                        <Route path="/noticias" element={<NewsIndex />} />
                        <Route path="/noticias/:slug" element={<NewsShow />} />
                        {INSTITUTIONAL_PATHS.map((path) => (
                            <Route key={path} path={path} element={<InstitutionalPage />} />
                        ))}
                        <Route path="*" element={<NotFound />} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </ErrorBoundary>
    );
}
