@extends('layouts.admin.app')

@section('title', 'Editar — ' . $event->title)

@section('content')
    <h2>Editar — {{ $event->title }}</h2>

    <form method="POST" action="{{ route('admin.fanfest-events.update', $event) }}" style="max-width:800px;display:grid;gap:1rem;">
        @csrf @method('PUT')
        @include('admin.fanfest-events._form')
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar cambios</button>
            <a href="{{ route('admin.fanfest-events.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
