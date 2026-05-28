@extends('layouts.admin.app')

@section('title', 'Clubes')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Clubes</h2>
            <p style="margin:0;color:#475569;">Equipos participantes en el torneo.</p>
        </div>
        <a href="{{ route('admin.clubs.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">+ Agregar club</a>
    </div>

    <form method="GET" style="margin:1.5rem 0;display:flex;gap:0.75rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Nombre del club" class="admin-button" style="min-width:240px;">
        <button type="submit" class="admin-button">Filtrar</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Código</th>
                    <th style="padding:0.75rem;">Ciudad</th>
                    <th style="padding:0.75rem;">Color</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Orden</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clubs as $club)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $club->name }}<br>
                            <small style="color:#64748b;font-family:monospace;">{{ $club->slug }}</small>
                        </td>
                        <td style="padding:0.75rem;font-family:monospace;font-weight:bold;">{{ $club->short_name }}</td>
                        <td style="padding:0.75rem;">{{ $club->city ?? '—' }}</td>
                        <td style="padding:0.75rem;">
                            @if($club->primary_color)
                                <span style="display:inline-block;width:1rem;height:1rem;border-radius:50%;background:{{ $club->primary_color }};border:1px solid #ccc;vertical-align:middle;margin-right:0.3rem;"></span>
                                {{ $club->primary_color }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:0.75rem;">{{ $club->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $club->sort_order }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.clubs.edit', $club) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" onsubmit="return confirm('¿Eliminar club?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:1rem;color:#64748b;">Sin clubes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
