@extends('layouts.admin.app')

@section('title', 'Editar Zona — ' . $zone->name)

@section('content')
    <h2>Editar Zona — {{ $zone->name }}</h2>

    <form method="POST" action="{{ route('admin.fanfest-events.zones.update', [$event, $zone]) }}" style="max-width:700px;display:grid;gap:1rem;">
        @csrf @method('PUT')
        @include('admin.fanfest-zones._form')
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar cambios</button>
            <a href="{{ route('admin.fanfest-events.zones.index', $event) }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
