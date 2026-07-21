import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Logo, Alert } from '@veraguas/ui';
import { apiFetch, setAuthToken } from '../../api/client';

export function Login() {
    const navigate = useNavigate(); const [submitting,setSubmitting]=useState(false); const [error,setError]=useState<string|null>(null); const [otp,setOtp]=useState('');
    async function handleSubmit(event: FormEvent<HTMLFormElement>) { event.preventDefault(); setSubmitting(true); setError(null); const form=new FormData(event.currentTarget); try { const result=await apiFetch<{token:string}>('/auth/login',{method:'POST',body:{email:form.get('email'),password:form.get('password'),otp:otp||undefined}}); setAuthToken(result.token); navigate('/admin'); } catch(e){setError(e instanceof Error?e.message:'No fue posible iniciar sesión.');} finally{setSubmitting(false);} }
    return <div className="flex min-h-screen items-center justify-center bg-primary px-4"><Container className="max-w-sm"><div className="mb-8 flex flex-col items-center gap-3 text-white"><Logo className="h-14 w-14" /><p className="font-display text-lg font-bold uppercase tracking-tight">Panel administrativo</p></div><Card><form onSubmit={handleSubmit} className="flex flex-col gap-4">{error?<Alert tone="danger">{error}</Alert>:null}<FormField htmlFor="email" label="Correo" required><Input id="email" name="email" type="email" autoComplete="username" required /></FormField><FormField htmlFor="password" label="Contraseña" required><Input id="password" name="password" type="password" autoComplete="current-password" required /></FormField><FormField htmlFor="otp" label="Código 2FA (si aplica)"><Input id="otp" value={otp} onChange={e=>setOtp(e.target.value)} inputMode="numeric" maxLength={6} /></FormField><Button type="submit" size="lg" pending={submitting} pendingLabel="Ingresando…">Ingresar</Button></form></Card></Container></div>;
}
