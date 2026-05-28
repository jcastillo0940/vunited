@extends('layouts.admin.app')

@section('title', 'Editar fila de tabla')

@section('content')
    <h2>Editar — {{ $standing->club?->name }}</h2>

    <form method="POST" action="{{ route('admin.standings.update', $standing) }}" style="max-width:800px;">
        @csrf @method('PUT')
        @include('admin.standings.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Actualizar</button>
            <a href="{{ route('admin.standings.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
