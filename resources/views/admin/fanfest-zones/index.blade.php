@extends('layouts.admin.app')

@section('title', 'Zonas — ' . $event->title)

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Zonas — {{ $event->title }}</h2>
            <a href="{{ route('admin.fanfest-events.index') }}" style="color:#1D428A;font-size:0.875rem;">← Volver a eventos</a>
        </div>
        <a href="{{ route('admin.fanfest-events.zones.create', $event) }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Agregar zona
        </a>
    </div>

    <div style="overflow:auto;margin-top:1.5rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Icono</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Orden</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($zones as $zone)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            <strong>{{ $zone->name }}</strong><br>
                            <small style="color:#64748b;">{{ Str::limit($zone->description ?? '', 60) }}</small>
                        </td>
                        <td style="padding:0.75rem;font-family:monospace;font-size:0.85rem;">{{ $zone->icon }}</td>
                        <td style="padding:0.75rem;">{{ $zone->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $zone->sort_order }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.fanfest-events.zones.edit', [$event, $zone]) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.fanfest-events.zones.destroy', [$event, $zone]) }}" onsubmit="return confirm('¿Eliminar zona?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:1rem;">Sin zonas registradas. Agrega la primera zona.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
