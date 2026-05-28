@extends('layouts.admin.app')

@section('title', 'Agregar Estadio')

@section('content')
    <h2>Agregar Estadio</h2>

    <form method="POST" action="{{ route('admin.stadium.store') }}" style="max-width:900px;">
        @csrf
        @include('admin.stadium.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.stadium.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
