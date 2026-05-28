@extends('layouts.admin.app')

@section('title', 'Agregar Patrocinador')

@section('content')
    <h2>Agregar Patrocinador</h2>

    <form method="POST" action="{{ route('admin.sponsors.store') }}" style="max-width:800px;">
        @csrf
        @include('admin.sponsors.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.sponsors.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
