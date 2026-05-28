@extends('layouts.admin.app')

@section('title', 'Agregar Gol')

@section('content')
    <div style="margin-bottom:1rem;">
        <a href="{{ route('admin.match-events.goals.index', $matchEvent) }}" style="color:#1D428A;font-size:0.875rem;">&larr; Goles del partido</a>
    </div>

    <h2>Agregar Gol — {{ $matchEvent->home_team }} vs {{ $matchEvent->away_team }}</h2>

    <form method="POST" action="{{ route('admin.match-events.goals.store', $matchEvent) }}" style="max-width:700px;">
        @csrf
        @include('admin.match-goals.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.match-events.goals.index', $matchEvent) }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
