@extends('layouts.admin.app')

@section('title', 'Tabla de posiciones')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Tabla de posiciones</h2>
            <p style="margin:0;color:#475569;">{{ $competition }} — Temporada {{ $season }}</p>
        </div>
        <a href="{{ route('admin.standings.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">+ Agregar fila</a>
    </div>

    <form method="GET" style="margin:1.5rem 0;display:flex;gap:0.75rem;flex-wrap:wrap;">
        <input type="text" name="season" value="{{ $season }}" placeholder="Temporada" class="admin-button" style="width:100px;">
        <input type="text" name="competition" value="{{ $competition }}" placeholder="Competición" class="admin-button" style="width:160px;">
        <button type="submit" class="admin-button">Filtrar</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Pos</th>
                    <th style="padding:0.75rem;">Club</th>
                    <th style="padding:0.75rem;text-align:center;">PJ</th>
                    <th style="padding:0.75rem;text-align:center;">G</th>
                    <th style="padding:0.75rem;text-align:center;">E</th>
                    <th style="padding:0.75rem;text-align:center;">P</th>
                    <th style="padding:0.75rem;text-align:center;">GF</th>
                    <th style="padding:0.75rem;text-align:center;">GC</th>
                    <th style="padding:0.75rem;text-align:center;">DG</th>
                    <th style="padding:0.75rem;text-align:center;">Pts</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($standings as $row)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;font-weight:bold;">{{ $row->position }}</td>
                        <td style="padding:0.75rem;">{{ $row->club?->name ?? '—' }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->played }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->won }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->drawn }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->lost }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->goals_for }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->goals_against }}</td>
                        <td style="padding:0.75rem;text-align:center;">{{ $row->goal_difference >= 0 ? '+' . $row->goal_difference : $row->goal_difference }}</td>
                        <td style="padding:0.75rem;text-align:center;font-weight:bold;">{{ $row->points }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.standings.edit', $row) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.standings.destroy', $row) }}" onsubmit="return confirm('¿Eliminar?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="padding:1rem;color:#64748b;">Sin datos para esta temporada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
