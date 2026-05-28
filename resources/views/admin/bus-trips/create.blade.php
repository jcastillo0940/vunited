@extends('layouts.admin.app')

@section('title', 'Agregar Viaje')

@section('content')
    <h2>Agregar Viaje — Expedición Indiana</h2>

    <form method="POST" action="{{ route('admin.bus-trips.store') }}" style="max-width:800px;display:grid;gap:1rem;">
        @csrf
        @include('admin.bus-trips._form')
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.bus-trips.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
