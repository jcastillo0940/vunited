@extends('layouts.admin.app')

@section('title', 'Editar — ' . $sponsor->name)

@section('content')
    <h2>Editar — {{ $sponsor->name }}</h2>

    <form method="POST" action="{{ route('admin.sponsors.update', $sponsor) }}" style="max-width:800px;">
        @csrf @method('PUT')
        @include('admin.sponsors.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar cambios</button>
            <a href="{{ route('admin.sponsors.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
