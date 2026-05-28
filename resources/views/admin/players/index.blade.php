@extends('layouts.admin.app')

@section('title', 'Jugadores')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Jugadores</h2>
            <p style="margin:0;color:#475569;">Plantilla administrable del club.</p>
        </div>
        <a href="{{ route('admin.players.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Agregar jugador
        </a>
    </div>

    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin:1.5rem 0;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nombre o slug" class="admin-button" style="min-width:240px;">
        <select name="category" class="admin-button">
            <option value="">Todas las categorías</option>
            @foreach(['first-team' => 'Primer Equipo', 'women-team' => 'Equipo Femenino', 'academy' => 'Cantera'] as $key => $label)
                <option value="{{ $key }}" @selected($filters['category'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filtrar</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">#</th>
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Posición</th>
                    <th style="padding:0.75rem;">Categoría</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Orden</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($players as $player)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;font-weight:bold;">{{ $player->number ?? '—' }}</td>
                        <td style="padding:0.75rem;">
                            {{ $player->name }}<br>
                            <small style="color:#64748b;font-family:monospace;">{{ $player->slug }}</small>
                        </td>
                        <td style="padding:0.75rem;">{{ $player->position ?? '—' }}</td>
                        <td style="padding:0.75rem;">{{ $player->category }}</td>
                        <td style="padding:0.75rem;">{{ $player->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $player->sort_order }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.players.edit', $player) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.players.destroy', $player) }}" onsubmit="return confirm('¿Eliminar jugador?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:1rem;">Sin jugadores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
