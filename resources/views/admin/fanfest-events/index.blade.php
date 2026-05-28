@extends('layouts.admin.app')

@section('title', 'FanFest Events')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">FanFest Events</h2>
            <p style="margin:0;color:#475569;">Gestión de eventos FanFest del club.</p>
        </div>
        <a href="{{ route('admin.fanfest-events.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Crear evento
        </a>
    </div>

    <div style="overflow:auto;margin-top:1.5rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Título</th>
                    <th style="padding:0.75rem;">Fecha</th>
                    <th style="padding:0.75rem;">Lugar</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Zonas</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $event->title }}<br>
                            <small style="color:#64748b;font-family:monospace;">{{ $event->slug }}</small>
                        </td>
                        <td style="padding:0.75rem;">{{ $event->event_date?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $event->location ?? '—' }}</td>
                        <td style="padding:0.75rem;">
                            <span style="padding:0.2rem 0.5rem;border-radius:0.4rem;font-size:0.8rem;background:{{ $event->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $event->is_active ? '#166534' : '#64748b' }};">
                                {{ $event->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">
                            <a href="{{ route('admin.fanfest-events.zones.index', $event) }}" style="color:#1D428A;">
                                {{ $event->all_zones_count ?? 0 }} zonas
                            </a>
                        </td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.fanfest-events.edit', $event) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.fanfest-events.destroy', $event) }}" onsubmit="return confirm('¿Eliminar evento y todas sus zonas?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:1rem;">Sin eventos FanFest registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
