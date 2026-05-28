@extends('layouts.admin.app')

@section('title', 'Editar Estadio')

@section('content')
    <h2>Editar: {{ $stadium->name }}</h2>

    <form method="POST" action="{{ route('admin.stadium.update', $stadium) }}" style="max-width:900px;">
        @csrf @method('PUT')
        @include('admin.stadium.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Actualizar</button>
            <a href="{{ route('admin.stadium.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
