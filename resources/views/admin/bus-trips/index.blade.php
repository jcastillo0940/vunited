@extends('layouts.admin.app')

@section('title', 'Expedición Indiana — Buses')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Expedición Indiana — Buses</h2>
            <p style="margin:0;color:#475569;">Viajes organizados para la hinchada india.</p>
        </div>
        <a href="{{ route('admin.bus-trips.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Agregar viaje
        </a>
    </div>

    <div style="overflow:auto;margin-top:1.5rem;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Título</th>
                    <th style="padding:0.75rem;">Salida</th>
                    <th style="padding:0.75rem;">Precio</th>
                    <th style="padding:0.75rem;">Cupos</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trips as $trip)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $trip->title }}<br>
                            <small style="color:#64748b;">{{ $trip->departure_location }}</small>
                            @if ($trip->matchEvent)
                                <br><small style="color:#5BC2E7;">{{ $trip->matchEvent->home_team }} vs {{ $trip->matchEvent->away_team }}</small>
                            @endif
                        </td>
                        <td style="padding:0.75rem;">{{ $trip->departure_time?->format('Y-m-d H:i') }}</td>
                        <td style="padding:0.75rem;">${{ number_format((float) $trip->price, 2) }} {{ $trip->currency }}</td>
                        <td style="padding:0.75rem;">
                            <span style="font-weight:bold;color:{{ $trip->available_seats > 0 ? '#166534' : '#991b1b' }};">
                                {{ $trip->available_seats }}/{{ $trip->capacity }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">{{ $trip->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.bus-trips.edit', $trip) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.bus-trips.destroy', $trip) }}" onsubmit="return confirm('¿Eliminar viaje?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:1rem;">Sin viajes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
