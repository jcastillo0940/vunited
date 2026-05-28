@extends('layouts.admin.app')

@section('title', 'Agregar Club')

@section('content')
    <h2>Agregar Club</h2>

    <form method="POST" action="{{ route('admin.clubs.store') }}" style="max-width:800px;">
        @csrf
        @include('admin.clubs.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.clubs.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
