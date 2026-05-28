@extends('layouts.admin.guest')

@section('title', 'Admin Login')

@section('content')
    <div style="margin-bottom:1.75rem;">
        <p style="margin:0 0 0.4rem;font-size:0.75rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#1D428A;">
            Veraguas United FC
        </p>
        <h1 style="margin:0;font-size:1.75rem;font-weight:700;color:#0f172a;">Admin Login</h1>
        <p style="margin:0.5rem 0 0;font-size:0.875rem;color:#64748b;">Accede con tu cuenta de administrador.</p>
    </div>

    @if ($errors->any())
        <div class="error-box">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
            <input id="remember" name="remember" type="checkbox" style="width:1rem;height:1rem;">
            <label for="remember" style="margin:0;font-weight:400;color:#64748b;">Recordarme</label>
        </div>

        <button type="submit" class="guest-submit">Iniciar sesión</button>
    </form>
@endsection
