@extends('layouts.admin.app')

@section('title', 'Estadio')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Estadio</h2>
            <p style="margin:0;color:#475569;">Información y configuración del estadio.</p>
        </div>
        <a href="{{ route('admin.stadium.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">+ Agregar estadio</a>
    </div>

    <div style="overflow:auto;margin-top:1.5rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Ubicación</th>
                    <th style="padding:0.75rem;">Capacidad</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stadiums as $stadium)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $stadium->name }}<br>
                            <small style="color:#64748b;">{{ $stadium->subtitle }}</small>
                        </td>
                        <td style="padding:0.75rem;">{{ $stadium->location ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $stadium->capacity ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $stadium->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.stadium.edit', $stadium) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.stadium.destroy', $stadium) }}" onsubmit="return confirm('¿Eliminar estadio?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:1rem;color:#64748b;">Sin estadios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
