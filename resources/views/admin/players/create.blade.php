@extends('layouts.admin.app')

@section('title', 'Agregar Jugador')

@section('content')
    <h2>Agregar Jugador</h2>

    <form method="POST" action="{{ route('admin.players.store') }}" style="max-width:900px;">
        @csrf
        @include('admin.players.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar jugador</button>
            <a href="{{ route('admin.players.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
