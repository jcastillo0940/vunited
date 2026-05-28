@extends('layouts.admin.app')

@section('title', 'Payment Settings')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Payment Settings</h2>
        <p>Configuracion del proveedor de pagos. Solo sandbox habilitado por ahora.</p>

        <div style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.75rem; background: #fef3c7; color: #92400e; font-size: 0.875rem; line-height: 1.5;">
            <strong>Seguridad:</strong> El client secret nunca se expone al frontend ni se registra en logs de auditoría.
            Los pagos reales se implementarán en una fase posterior (Payment Foundation).
            Esta pantalla solo configura las credenciales del proveedor.
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.75rem; background: #fee2e2; color: #991b1b;">
                Por favor corrige los campos indicados.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payment-settings.update') }}" style="display: grid; gap: 1rem;">
            @csrf
            @method('PUT')

            <label style="display: grid; gap: 0.35rem;">
                <span>Proveedor</span>
                <input type="text" value="{{ $setting->provider }}" disabled style="background:#f1f5f9; color:#64748b;">
                <small style="color:#64748b;">Solo PayPal disponible en esta fase.</small>
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Modo</span>
                <select name="mode">
                    <option value="sandbox" @selected(old('mode', $setting->mode) === 'sandbox')>Sandbox (pruebas)</option>
                    <option value="live" @selected(old('mode', $setting->mode) === 'live')>Live (producción)</option>
                </select>
                @error('mode')
                    <small style="color:#991b1b;">{{ $message }}</small>
                @enderror
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Client ID</span>
                <input type="text" name="client_id" value="{{ old('client_id', $setting->client_id) }}" placeholder="PayPal Client ID">
                @error('client_id')
                    <small style="color:#991b1b;">{{ $message }}</small>
                @enderror
            </label>

            <fieldset style="border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 1rem; display: grid; gap: 0.5rem;">
                <legend style="padding: 0 0.5rem; font-size: 0.875rem; color: #475569;">Client Secret</legend>

                @if ($setting->client_secret)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: #f0fdf4; border: 1px solid #86efac; border-radius: 0.375rem; color: #166534; font-size: 0.875rem;">
                        <span>&#10003;</span>
                        <span>Secret configurado</span>
                    </div>
                    <small style="color:#64748b;">Deja el campo vacío para conservar el secret actual. Escribe uno nuevo para reemplazarlo.</small>
                @else
                    <small style="color:#64748b;">Sin secret configurado.</small>
                @endif

                <input
                    type="password"
                    name="client_secret"
                    value=""
                    placeholder="{{ $setting->client_secret ? 'Escribir nuevo secret para reemplazar' : 'PayPal Client Secret' }}"
                    autocomplete="new-password"
                >
                @error('client_secret')
                    <small style="color:#991b1b;">{{ $message }}</small>
                @enderror
            </fieldset>

            <label style="display: grid; gap: 0.35rem;">
                <span>Webhook ID</span>
                <input type="text" name="webhook_id" value="{{ old('webhook_id', $setting->webhook_id) }}" placeholder="PayPal Webhook ID">
                <small style="color:#64748b;">Requerido para recibir y validar eventos de PayPal (implementación futura).</small>
                @error('webhook_id')
                    <small style="color:#991b1b;">{{ $message }}</small>
                @enderror
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Moneda</span>
                <input type="text" name="currency" value="{{ old('currency', $setting->currency) }}" maxlength="3" placeholder="USD">
                <small style="color:#64748b;">Código ISO 4217 de 3 letras (ej: USD, PAB).</small>
                @error('currency')
                    <small style="color:#991b1b;">{{ $message }}</small>
                @enderror
            </label>

            <label style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="hidden" name="is_enabled" value="0">
                <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $setting->is_enabled))>
                <span>Activar PayPal</span>
            </label>
            <small style="color:#64748b; margin-top: -0.5rem;">
                Activar solo cuando Payment Foundation esté implementado y las credenciales live estén verificadas.
            </small>

            <div>
                <button type="submit" class="admin-button">Guardar configuración</button>
            </div>
        </form>
    </section>
@endsection
